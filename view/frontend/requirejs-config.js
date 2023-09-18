// eslint-disable-next-line no-unused-vars
var config = {
    config: {
        mixins: {
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Grasch_HCaptcha/js/payflowpro-method-mixin': true
            },
            'PayPal_Braintree/js/view/payment/method-renderer/cc-form': {
                'Grasch_HCaptcha/js/braintree-cc-method-mixin': true
            },
            'jquery': {
                'Grasch_HCaptcha/js/web-api/jquery-mixin': true
            },
            'Magento_Checkout/js/model/place-order': {
                'Grasch_HCaptcha/js/model/place-order-mixin': true
            }
        }
    }
};
