define(
    [
        'Grasch_HCaptcha/js/web-api/webapiHCaptcha',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        var hCaptchaIds = new WeakMap(),
            uuid = 0;

        return Component.extend({
            defaults: {
                template: 'Grasch_HCaptcha/hCaptcha-checkout',
                skipPayments: [] // List of payment methods that do not require this hCaptcha
            },

            /**
             * Render hCAPTCHA for payment method
             *
             * @param {Object} method
             */
            renderHCaptchaFor: function (method) {
                var hCaptcha;

                if (this.isCheckoutHCaptchaRequiredFor(method)) {
                    hCaptcha = $.extend(true, {}, this, {hCaptchaId: this.getHCaptchaIdFor(method)});
                    hCaptcha.renderHCaptcha();
                }
            },

            /**
             * Get hCAPTCHA ID for payment method
             *
             * @param {Object} method
             * @returns {String}
             */
            getHCaptchaIdFor: function (method) {
                if (!hCaptchaIds.has(method)) {
                    hCaptchaIds.set(method, this.getHCaptchaId() + '-' + uuid++);
                }
                return hCaptchaIds.get(method);
            },

            /**
             * Check whether checkout hCAPTCHA is required for payment method
             *
             * @param {Object} method
             * @returns {Boolean}
             */
            isCheckoutHCaptchaRequiredFor: function (method) {
                return !this.skipPayments || !this.skipPayments.hasOwnProperty(method.getCode());
            },

            /**
             * @inheritdoc
             */
            initCaptcha: function () {
                var $wrapper,
                    $hcaptchaResponseInput;

                this._super();
                // Since there will be multiple hCaptcha in the payment form,
                // they may override each other if the form data is serialized and submitted.
                // Instead, the hCaptcha response will be collected in the callback: hCaptchaCallback()
                // and sent in the request header X-HCaptcha
                $wrapper = $('#' + this.getHCaptchaId() + '-wrapper');
                $hcaptchaResponseInput = $wrapper.find('[name=hcaptcha-response]');
                if ($hcaptchaResponseInput.length) {
                    $hcaptchaResponseInput.prop('disabled', true);
                }
            }
        });
    }
);
