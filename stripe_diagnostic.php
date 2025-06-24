<?php
/**
 * Stripe Configuration Diagnostic Script
 * 
 * This script helps diagnose Stripe configuration issues
 * Run this from your Laravel application root directory
 */

// Include Laravel bootstrap
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== Stripe Configuration Diagnostic ===\n\n";

// Check if Stripe package is installed
echo "1. Checking Stripe Package Installation...\n";
if (class_exists('Stripe\Stripe')) {
    echo "   ✓ Stripe PHP library is installed\n";
} else {
    echo "   ✗ Stripe PHP library is NOT installed\n";
    echo "   Run: composer require stripe/stripe-php\n";
}

// Check environment variables
echo "\n2. Checking Environment Variables...\n";
$webhook_secret = env('STRIPE_WEBHOOK_SECRET');
if ($webhook_secret) {
    echo "   ✓ STRIPE_WEBHOOK_SECRET is set\n";
} else {
    echo "   ⚠ STRIPE_WEBHOOK_SECRET is not set (optional for basic payments)\n";
}

// Check database connection
echo "\n3. Checking Database Connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Database connection is working\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Check required tables
echo "\n4. Checking Required Tables...\n";
$required_tables = [
    'seller_subcriptions',
    'subscription_payment_infos',
    'transactions',
    'seller_wise_payment_gateways'
];

foreach ($required_tables as $table) {
    try {
        DB::table($table)->limit(1)->get();
        echo "   ✓ Table '{$table}' exists\n";
    } catch (Exception $e) {
        echo "   ✗ Table '{$table}' does not exist or is not accessible\n";
    }
}

// Check Stripe configuration in database
echo "\n5. Checking Stripe Configuration in Database...\n";
try {
    $stripe_config = DB::table('seller_wise_payment_gateways')
        ->join('payment_methods', 'seller_wise_payment_gateways.payment_method_id', '=', 'payment_methods.id')
        ->where('payment_methods.slug', 'stripe')
        ->where('seller_wise_payment_gateways.user_id', 1)
        ->first();
    
    if ($stripe_config) {
        echo "   ✓ Stripe configuration found for admin (user_id: 1)\n";
        echo "   - Status: " . ($stripe_config->status ? 'Active' : 'Inactive') . "\n";
        echo "   - Has Publishable Key: " . (!empty($stripe_config->perameter_1) ? 'Yes' : 'No') . "\n";
        echo "   - Has Secret Key: " . (!empty($stripe_config->perameter_3) ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ✗ No Stripe configuration found for admin\n";
        echo "   Please configure Stripe in Admin Panel > Payment Gateway Settings\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error checking Stripe configuration: " . $e->getMessage() . "\n";
}

// Check helper functions
echo "\n6. Checking Helper Functions...\n";
if (function_exists('getPaymentInfoViaSellerId')) {
    echo "   ✓ getPaymentInfoViaSellerId function exists\n";
} else {
    echo "   ✗ getPaymentInfoViaSellerId function does not exist\n";
}

if (function_exists('getParentSellerId')) {
    echo "   ✓ getParentSellerId function exists\n";
} else {
    echo "   ✗ getParentSellerId function does not exist\n";
}

if (function_exists('getCurrencyCode')) {
    echo "   ✓ getCurrencyCode function exists\n";
} else {
    echo "   ✗ getCurrencyCode function does not exist\n";
}

// Check routes
echo "\n7. Checking Routes...\n";
$routes_to_check = [
    'seller.subscriptionPaymentGateway',
    'seller.seller-subscription-payment',
    'stripe.webhook'
];

foreach ($routes_to_check as $route_name) {
    try {
        $url = route($route_name, ['id' => 1], false);
        echo "   ✓ Route '{$route_name}' exists: {$url}\n";
    } catch (Exception $e) {
        echo "   ✗ Route '{$route_name}' does not exist\n";
    }
}

// Check file permissions
echo "\n8. Checking File Permissions...\n";
$log_dir = storage_path('logs');
if (is_writable($log_dir)) {
    echo "   ✓ Log directory is writable\n";
} else {
    echo "   ✗ Log directory is not writable: {$log_dir}\n";
}

// Test Stripe API connection (if configured)
echo "\n9. Testing Stripe API Connection...\n";
try {
    $stripe_config = DB::table('seller_wise_payment_gateways')
        ->join('payment_methods', 'seller_wise_payment_gateways.payment_method_id', '=', 'payment_methods.id')
        ->where('payment_methods.slug', 'stripe')
        ->where('seller_wise_payment_gateways.user_id', 1)
        ->first();
    
    if ($stripe_config && !empty($stripe_config->perameter_3)) {
        \Stripe\Stripe::setApiKey($stripe_config->perameter_3);
        $account = \Stripe\Account::retrieve();
        echo "   ✓ Stripe API connection successful\n";
        echo "   - Account ID: " . $account->id . "\n";
        echo "   - Country: " . $account->country . "\n";
        echo "   - Test Mode: " . ($account->livemode ? 'No' : 'Yes') . "\n";
    } else {
        echo "   ⚠ Stripe API test skipped (no secret key configured)\n";
    }
} catch (Exception $e) {
    echo "   ✗ Stripe API connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Diagnostic Complete ===\n";
echo "\nIf you found any issues above, please refer to the STRIPE_SETUP_GUIDE.md file for solutions.\n";
