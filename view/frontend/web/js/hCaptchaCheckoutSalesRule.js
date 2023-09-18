define(
    [
        'Grasch_HCaptcha/js/web-api/webapiHCaptcha',
        'Grasch_HCaptcha/js/web-api/webapiHCaptchaRegistry',
        'jquery',
        'Magento_SalesRule/js/action/set-coupon-code',
        'Magento_SalesRule/js/action/cancel-coupon',
        'Magento_Checkout/js/model/quote',
        'ko'
    ], function (Component, hcaptchaRegistry, $, setCouponCodeAction, cancelCouponAction, quote, ko) {
        'use strict';

        var totals = quote.getTotals(),
            couponCode = ko.observable(null),
            isApplied;

        if (totals()) {
            couponCode(totals()['coupon_code']);
        }
        //Captcha can only be required for adding a coupon so we need to know if one was added already.
        isApplied = ko.observable(couponCode() != null);

        return Component.extend({

            /**
             * Initialize parent form.
             *
             * @param {Object} parentForm
             * @param {String} widgetId
             */
            initParentForm: function (parentForm, widgetId) {
                var self = this,
                    xHcaptchaValue,
                    captchaId = this.getHCaptchaId();

                this._super();

                if (couponCode() != null) {
                    if (isApplied) {
                        self.validateHCaptcha(true);
                        $('#' + captchaId).hide();
                    }
                }

                if (hcaptchaRegistry.triggers.hasOwnProperty('hcaptcha-checkout-coupon-apply')) {
                    hcaptchaRegistry.addListener('hcaptcha-checkout-coupon-apply', function (token) {
                        //Add hCaptcha value to coupon request
                        xHcaptchaValue = token;
                    });
                }

                setCouponCodeAction.registerDataModifier(function (headers) {
                    headers['X-HCaptcha'] = xHcaptchaValue;
                });

                if (self.getIsInvisibleHCaptcha()) {
                    hcaptcha.execute(widgetId);
                    self.validateHCaptcha(true);
                }
                //Refresh hCaptcha after failed request.
                setCouponCodeAction.registerFailCallback(function () {
                    if (self.getIsInvisibleHCaptcha()) {
                        hcaptcha.execute(widgetId);
                        self.validateHCaptcha(true);
                    } else {
                        self.validateHCaptcha(false);
                        hcaptcha.reset(widgetId);
                        $('#' + captchaId).show();
                    }
                });
                //Hide captcha when a coupon has been applied.
                setCouponCodeAction.registerSuccessCallback(function () {
                    self.validateHCaptcha(true);
                    $('#' + captchaId).hide();
                });
                //Show captcha again if it was canceled.
                cancelCouponAction.registerSuccessCallback(function () {
                    self.validateHCaptcha(false);
                    hcaptcha.reset(widgetId);
                    $('#' + captchaId).show();
                });
            }
        });
    });
