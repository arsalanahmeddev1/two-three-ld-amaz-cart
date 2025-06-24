<?php

namespace Modules\MultiVendor\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PaymentGateway\Http\Controllers\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Exception;

class SellerSubscriptionController extends Controller
{
    public function payment(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'method' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'stripeToken' => 'required_if:method,Stripe'
            ]);

            // Set subscription payment session
            session()->put('subscription_payment', true);

            if ($request->method == 'Stripe') {
                $stripeController = new StripeController();
                $result = $stripeController->stripePost($request->all());

                // Handle different response types
                if (is_array($result)) {
                    if ($result['status'] === 'success') {
                        Toastr::success($result['message']);
                        return redirect($result['redirect_url']);
                    } else {
                        Toastr::error($result['message']);
                        return redirect($result['redirect_url']);
                    }
                } else {
                    // Handle legacy response or redirect response
                    if (is_object($result)) {
                        return $result;
                    } else {
                        // Assume success if not an error array
                        Toastr::success('Payment processed successfully!');
                        return redirect()->route('seller.dashboard');
                    }
                }
            }

            // Handle other payment methods here
            Toastr::error('Invalid payment method');
            return redirect()->back();

        } catch (Exception $e) {
            Log::error('Subscription Payment Error: ' . $e->getMessage());
            Toastr::error('Payment processing failed. Please try again.');
            return redirect()->back();
        }
    }
} 