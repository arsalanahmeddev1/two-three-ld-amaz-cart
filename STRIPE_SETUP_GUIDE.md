# Stripe Subscription Setup Guide

## Overview
This guide explains how to set up Stripe subscription payments for sellers in your multi-vendor marketplace.

## Environment Configuration

### 1. Add Stripe Webhook Secret to .env
Add the following line to your `.env` file:
```
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

### 2. Configure Stripe in Admin Panel
1. Go to Admin Panel > Payment Gateway Settings
2. Configure Stripe with your API keys:
   - **Publishable Key**: `pk_test_...` (for test) or `pk_live_...` (for live)
   - **Secret Key**: `sk_test_...` (for test) or `sk_live_...` (for live)
   - **Webhook Secret**: Get this from your Stripe Dashboard

## Webhook Setup

### 1. Create Webhook in Stripe Dashboard
1. Go to your Stripe Dashboard
2. Navigate to Developers > Webhooks
3. Click "Add endpoint"
4. Set the endpoint URL to: `https://yourdomain.com/stripe-webhook`
5. Select these events:
   - `charge.succeeded`
   - `charge.failed`
   - `payment_intent.succeeded`

### 2. Get Webhook Secret
1. After creating the webhook, click on it
2. Copy the "Signing secret" (starts with `whsec_`)
3. Add it to your `.env` file as `STRIPE_WEBHOOK_SECRET`

## Multi-Tenant Configuration

### For Seller-Specific Stripe Accounts
1. Enable "Seller Wise Payment" in General Settings
2. Each seller can configure their own Stripe credentials in their seller panel
3. The system will automatically use the appropriate credentials based on the seller

### For Single Stripe Account (Recommended for most cases)
1. Keep "Seller Wise Payment" disabled
2. Configure Stripe credentials in the main admin panel
3. All payments will go through the main Stripe account

## Testing

### Test Card Numbers
- **Success**: 4242424242424242
- **Decline**: 4000000000000002
- **Insufficient Funds**: 4000000000009995
- **Expiry**: Any future date (e.g., 12/25)
- **CVC**: Any 3 digits (e.g., 123)

### Test Flow
1. Register as a seller
2. Go to subscription payment page
3. Use test card numbers above
4. Verify payment is processed and seller gains dashboard access

## Troubleshooting

### Common Issues

#### 1. "Payment gateway configuration not found"
- Check if Stripe is configured in admin panel
- Verify API keys are correct
- Check if seller-wise payment is enabled and seller has configured Stripe

#### 2. "Stripe token is missing"
- Ensure frontend is properly generating Stripe tokens
- Check if Stripe.js is loaded correctly
- Verify publishable key is correct

#### 3. "Invalid payment session"
- Check if subscription payment session is set
- Verify the payment flow is initiated correctly

#### 4. Webhook not working
- Verify webhook URL is accessible
- Check webhook secret in .env file
- Ensure webhook events are configured correctly

### Debug Logs
Check these log files for debugging:
- `storage/logs/laravel.log` - General application logs
- `storage/logs/stripe_debug.txt` - Stripe-specific debug logs

### Database Tables
Key tables for subscription payments:
- `seller_subcriptions` - Seller subscription records
- `subscription_payment_infos` - Payment transaction records
- `transactions` - Financial transaction records

## Security Considerations

1. **Always use HTTPS** for production
2. **Validate webhook signatures** (already implemented)
3. **Store sensitive data securely** (API keys in .env)
4. **Use test mode** for development and testing
5. **Regularly rotate API keys**

## Production Checklist

- [ ] Replace test API keys with live keys
- [ ] Update webhook URL to production domain
- [ ] Test with real payment methods
- [ ] Verify webhook is receiving events
- [ ] Check all error handling paths
- [ ] Monitor payment logs
- [ ] Set up proper backup procedures

## Support

If you encounter issues:
1. Check the troubleshooting section above
2. Review log files for error messages
3. Verify Stripe dashboard for payment status
4. Test with different card numbers
5. Check webhook delivery in Stripe dashboard
