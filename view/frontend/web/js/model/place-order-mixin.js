define([
    'jquery',
    'mage/utils/wrapper',
    'Grasch_HCaptcha/js/web-api/webapiHCaptchaRegistry'
], function ($, wrapper, hcaptchaRegistry) {
    'use strict';

    return function (placeOrder) {
        return wrapper.wrap(placeOrder, function (originalAction, serviceUrl, payload, messageContainer) {
            var hcaptchaDeferred,
                hCaptchaId,
                $activeHCaptcha;

            $activeHCaptcha = $('.hcaptcha-checkout-place-order:visible .h-captcha');

            if ($activeHCaptcha.length > 0) {
                hCaptchaId = $activeHCaptcha.last().attr('id');
            }

            if (hCaptchaId !== undefined && hcaptchaRegistry.triggers.hasOwnProperty(hCaptchaId)) {
                //HCaptcha is present for checkout
                hcaptchaDeferred = $.Deferred();
                hcaptchaRegistry._listeners[hCaptchaId] = function (token) {
                    payload.xHCaptchaValue = token;
                    originalAction(serviceUrl, payload, messageContainer).done(function () {
                        hcaptchaDeferred.resolve.apply(hcaptchaDeferred, arguments);
                    }).fail(function () {
                        hcaptchaDeferred.reject.apply(hcaptchaDeferred, arguments);
                    });
                };
                //Trigger HCaptcha validation
                hcaptchaRegistry.triggers[hCaptchaId]();

                if (
                    !hcaptchaRegistry._isInvisibleType.hasOwnProperty(hCaptchaId) ||
                    hcaptchaRegistry._isInvisibleType[hCaptchaId] === false
                ) {
                    //remove listener so that place order action is only triggered by the 'Place Order' button
                    hcaptchaRegistry.removeListener(hCaptchaId);
                }

                return hcaptchaDeferred;
            }

            //No HCaptcha, just sending the request
            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});
