<?php

namespace Modules\PaymentGateway\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Repositories\OrderRepository;
use \Modules\Wallet\Repositories\WalletRepository;
use Modules\Account\Repositories\TransactionRepository;
use Modules\Account\Entities\Transaction;
use Modules\FrontendCMS\Entities\SubsciptionPaymentInfo;
use App\Traits\Accounts;
use Carbon\Carbon;
use Exception;
use Modules\UserActivityLog\Traits\LogActivity;
use Stripe;
use Illuminate\Support\Facades\Log;
use Modules\Bkash\Http\Controllers\BkashController;
use Modules\Clickpay\Http\Controllers\ClickpayController;
use Modules\SslCommerz\Library\SslCommerz\SslCommerzNotification;
use App\Models\User;
use Modules\MultiVendor\Entities\SellerSubcription;
use Modules\MercadoPago\Http\Controllers\MercadoPagoController;
use App\Models\State;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

class StripeController extends Controller
{
    use Accounts;

    public function __construct()
    {
        $this->middleware('maintenance_mode');
    } 

    public function payment_page(Request $request)
    {
        return view('paymentgateway::stripe_payment.create');
    }

    public function stripePost($data)
    {
        try {
            // Check subscription payment session
            if (!session()->has('subscription_payment')) {
                Log::error('Subscription payment session not found');
                return [
                    'status' => 'error',
                    'message' => 'Invalid payment session',
                    'redirect_url' => route('seller.dashboard')
                ];
            }

            $currency_code = getCurrencyCode();
            $credential = $this->getCredential();

            if (!$credential) {
                Log::error('Stripe credential not found', [
                    'seller_id' => getParentSellerId(),
                    'seller_wise_payment' => app('general_setting')->seller_wise_payment ?? false
                ]);
                throw new Exception('Payment gateway configuration not found');
            }

            // Validate required fields
            if (empty($data['stripeToken'])) {
                throw new Exception('Stripe token is missing');
            }

            if (empty($data['amount']) || $data['amount'] <= 0) {
                throw new Exception('Invalid payment amount');
            }

            // Validate Stripe API key
            if (empty($credential->perameter_3)) {
                throw new Exception('Stripe secret key not configured');
            }

            Stripe\Stripe::setApiKey($credential->perameter_3);

            // Log the charge request data
            $charge_data = [
                "amount" => round($data['amount'] * 100),
                "currency" => $currency_code,
                "source" => $data['stripeToken'],
                "description" => "Subscription Payment from " . url('/') . " - Seller ID: " . getParentSellerId(),
                "metadata" => [
                    "seller_id" => getParentSellerId(),
                    "payment_type" => "subscription",
                    "seller_email" => getParentSeller()->email ?? 'unknown'
                ]
            ];

            Log::info('Stripe Charge Request Data', [
                'charge_data' => $charge_data,
                'seller_id' => getParentSellerId(),
                'credential_id' => $credential->id ?? 'unknown'
            ]);

            // Create the charge
            $stripe = Stripe\Charge::create($charge_data);

            // Log successful charge
            Log::info('Stripe Charge Created Successfully', [
                'charge_id' => $stripe['id'],
                'status' => $stripe['status'],
                'amount' => $stripe['amount'],
                'seller_id' => getParentSellerId()
            ]);

            if ($stripe['status'] == "succeeded") {
                $return_data = $stripe['id'];

                if (session()->has('subscription_payment')) {
                    // Start database transaction
                    DB::beginTransaction();
                    try {
                        // Get the default income account
                        $defaultIncomeAccount = $this->defaultIncomeAccount();
                        if (!$defaultIncomeAccount) {
                            throw new Exception('Income account not configured');
                        }

                        // Get seller subscription
                        $seller_subscription = getParentSeller()->SellerSubscriptions;
                        if (!$seller_subscription) {
                            throw new Exception('Seller subscription not found');
                        }

                        // Create transaction record
                        $transactionRepo = new TransactionRepository(new Transaction);
                        $transaction = $transactionRepo->makeTransaction(
                            getParentSeller()->first_name . " - Subscription Payment",
                            "in",
                            "Stripe",
                            "subscription_payment",
                            $defaultIncomeAccount,
                            "Subscription Payment",
                            $seller_subscription,
                            $data['amount'],
                            Carbon::now()->format('Y-m-d'),
                            getParentSellerId(),
                            null,
                            null
                        );

                        // Update subscription
                        $seller_subscription->update([
                            'last_payment_date' => Carbon::now()->format('Y-m-d')
                        ]);

                        // Create payment info
                        SubsciptionPaymentInfo::create([
                            'transaction_id' => $transaction->id,
                            'txn_id' => $return_data,
                            'seller_id' => getParentSellerId(),
                            'subscription_type' => getParentSeller()->sellerAccount->subscription_type,
                            'commission_type' => @$seller_subscription->pricing->name
                        ]);

                        // Commit transaction
                        DB::commit();

                        // Log success
                        LogActivity::successLog('Subscription payment successful via Stripe.');

                        // Clear session
                        session()->forget('subscription_payment');

                        // Return success response with redirect
                        return [
                            'status' => 'success',
                            'message' => 'Subscription payment completed successfully!',
                            'redirect_url' => route('seller.dashboard'),
                            'transaction_id' => $return_data
                        ];

                    } catch (Exception $e) {
                        // Rollback transaction on error
                        DB::rollBack();
                        Log::error('Subscription Processing Error', [
                            'error' => $e->getMessage(),
                            'seller_id' => getParentSellerId(),
                            'charge_id' => $return_data ?? 'unknown'
                        ]);
                        throw new Exception('Error processing subscription: ' . $e->getMessage());
                    }
                }
            }

            throw new Exception('Payment was not successful. Status: ' . ($stripe['status'] ?? 'unknown'));

        } catch (Exception $e) {
            Log::error('Stripe Payment Error', [
                'error' => $e->getMessage(),
                'seller_id' => getParentSellerId() ?? 'unknown',
                'data' => $data
            ]);

            if (session()->has('subscription_payment')) {
                session()->forget('subscription_payment');
            }

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'redirect_url' => route('seller.dashboard')
            ];
        }
    }

    private function getCredential(){
        try {
            $seller_id = 1; // Default to admin/main seller

            // Check if this is a subscription payment
            if (session()->has('subscription_payment')) {
                $seller_id = getParentSellerId();
                Log::info('Getting Stripe credentials for subscription payment', [
                    'seller_id' => $seller_id,
                    'seller_wise_payment' => app('general_setting')->seller_wise_payment ?? false
                ]);
            } else {
                // Handle regular checkout flow
                $url = explode('?', url()->previous());
                $is_checkout = isset($url[0]) && $url[0] == url('/checkout');

                if (session()->has('order_payment') &&
                    app('general_setting')->seller_wise_payment &&
                    session()->has('seller_for_checkout') &&
                    $is_checkout) {
                    $seller_id = session()->get('seller_for_checkout');
                }
            }

            $credential = getPaymentInfoViaSellerId($seller_id, 'stripe');

            if (!$credential) {
                Log::warning('Stripe credential not found, trying fallback', [
                    'requested_seller_id' => $seller_id,
                    'fallback_seller_id' => 1
                ]);
                // Fallback to admin credentials if seller-specific not found
                $credential = getPaymentInfoViaSellerId(1, 'stripe');
            }

            if ($credential) {
                Log::info('Stripe credential retrieved successfully', [
                    'credential_id' => $credential->id ?? 'unknown',
                    'seller_id' => $seller_id,
                    'has_secret_key' => !empty($credential->perameter_3)
                ]);
            }

            return $credential;

        } catch (Exception $e) {
            Log::error('Error getting Stripe credentials', [
                'error' => $e->getMessage(),
                'seller_id' => $seller_id ?? 'unknown'
            ]);
            return null;
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $sig_header = $request->header('Stripe-Signature');

            // Get webhook secret from environment or config
            $webhook_secret = env('STRIPE_WEBHOOK_SECRET');

            if (!$webhook_secret) {
                Log::warning('Stripe webhook secret not configured');
                return response('Webhook secret not configured', 400);
            }

            // Verify webhook signature
            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
            } catch (\UnexpectedValueException $e) {
                Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);
                return response('Invalid payload', 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);
                return response('Invalid signature', 400);
            }

            Log::info('Stripe webhook received', [
                'event_type' => $event['type'],
                'event_id' => $event['id']
            ]);

            // Handle the event
            switch ($event['type']) {
                case 'charge.succeeded':
                    $this->handleChargeSucceeded($event['data']['object']);
                    break;
                case 'charge.failed':
                    $this->handleChargeFailed($event['data']['object']);
                    break;
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event['data']['object']);
                    break;
                default:
                    Log::info('Unhandled Stripe webhook event type', ['type' => $event['type']]);
            }

            return response('Webhook handled', 200);

        } catch (Exception $e) {
            Log::error('Stripe webhook error', ['error' => $e->getMessage()]);
            return response('Webhook error', 500);
        }
    }

    /**
     * Handle successful charge webhook
     */
    private function handleChargeSucceeded($charge)
    {
        try {
            Log::info('Processing successful charge webhook', [
                'charge_id' => $charge['id'],
                'amount' => $charge['amount'],
                'metadata' => $charge['metadata'] ?? []
            ]);

            // Check if this is a subscription payment
            if (isset($charge['metadata']['payment_type']) &&
                $charge['metadata']['payment_type'] === 'subscription' &&
                isset($charge['metadata']['seller_id'])) {

                $seller_id = $charge['metadata']['seller_id'];

                // Check if payment info already exists
                $existing_payment = SubsciptionPaymentInfo::where('txn_id', $charge['id'])->first();
                if ($existing_payment) {
                    Log::info('Subscription payment already processed', ['charge_id' => $charge['id']]);
                    return;
                }

                // Process subscription payment via webhook as backup
                $this->processSubscriptionPaymentWebhook($charge, $seller_id);
            }

        } catch (Exception $e) {
            Log::error('Error handling charge succeeded webhook', [
                'error' => $e->getMessage(),
                'charge_id' => $charge['id'] ?? 'unknown'
            ]);
        }
    }

    /**
     * Handle failed charge webhook
     */
    private function handleChargeFailed($charge)
    {
        Log::warning('Stripe charge failed', [
            'charge_id' => $charge['id'],
            'failure_code' => $charge['failure_code'] ?? 'unknown',
            'failure_message' => $charge['failure_message'] ?? 'unknown',
            'metadata' => $charge['metadata'] ?? []
        ]);
    }

    /**
     * Handle successful payment intent webhook
     */
    private function handlePaymentIntentSucceeded($payment_intent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $payment_intent['id'],
            'amount' => $payment_intent['amount']
        ]);
    }

    /**
     * Process subscription payment via webhook (fallback)
     */
    private function processSubscriptionPaymentWebhook($charge, $seller_id)
    {
        try {
            DB::beginTransaction();

            $seller = User::find($seller_id);
            if (!$seller) {
                throw new Exception('Seller not found: ' . $seller_id);
            }

            $seller_subscription = SellerSubcription::where('seller_id', $seller_id)->first();
            if (!$seller_subscription) {
                throw new Exception('Seller subscription not found for seller: ' . $seller_id);
            }

            $defaultIncomeAccount = $this->defaultIncomeAccount();
            if (!$defaultIncomeAccount) {
                throw new Exception('Income account not configured');
            }

            // Create transaction record
            $transactionRepo = new TransactionRepository(new Transaction);
            $transaction = $transactionRepo->makeTransaction(
                $seller->first_name . " - Subscription Payment (Webhook)",
                "in",
                "Stripe",
                "subscription_payment",
                $defaultIncomeAccount,
                "Subscription Payment via Webhook",
                $seller_subscription,
                $charge['amount'] / 100, // Convert from cents
                Carbon::now()->format('Y-m-d'),
                $seller_id,
                null,
                null
            );

            // Update subscription
            $seller_subscription->update([
                'last_payment_date' => Carbon::now()->format('Y-m-d')
            ]);

            // Create payment info
            SubsciptionPaymentInfo::create([
                'transaction_id' => $transaction->id,
                'txn_id' => $charge['id'],
                'seller_id' => $seller_id,
                'subscription_type' => $seller->sellerAccount->subscription_type ?? 'yearly',
                'commission_type' => @$seller_subscription->pricing->name
            ]);

            DB::commit();

            Log::info('Subscription payment processed via webhook', [
                'charge_id' => $charge['id'],
                'seller_id' => $seller_id,
                'transaction_id' => $transaction->id
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing subscription payment via webhook', [
                'error' => $e->getMessage(),
                'charge_id' => $charge['id'] ?? 'unknown',
                'seller_id' => $seller_id
            ]);
            throw $e;
        }
    }

}
