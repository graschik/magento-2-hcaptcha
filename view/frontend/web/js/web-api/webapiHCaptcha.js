// jscs:disable jsDoc

/* global hcaptcha */
define(
    [
        'Grasch_HCaptcha/js/hCaptcha',
        'Grasch_HCaptcha/js/web-api/webapiHCaptchaRegistry'
    ],
    function (Component, registry) {
        'use strict';

        return Component.extend({
            defaults: {
                autoTrigger: false
            },

            /**
             * Provide the token to the registry.
             *
             * @param {String} token
             */
            hCaptchaCallback: function (token) {
                //Make the token retrievable in other UI components.
                registry.tokens[this.getHCaptchaId()] = token;

                if (typeof registry._listeners[this.getHCaptchaId()] !== 'undefined') {
                    registry._listeners[this.getHCaptchaId()](token);
                }
            },

            /**
             * Register this HCaptcha.
             *
             * @param {Object} parentForm
             * @param {String} widgetId
             */
            initParentForm: function (parentForm, widgetId) {
                var self = this,
                    trigger;

                if (this.getIsInvisibleHCaptcha()) {
                    trigger = function () {
                        hcaptcha.execute(widgetId);
                    };
                    registry._isInvisibleType[this.getHCaptchaId()] = true;
                } else {
                    trigger = function () {
                        self.hCaptchaCallback(hcaptcha.getResponse(widgetId));
                    };
                    registry._isInvisibleType[this.getHCaptchaId()] = false;
                }

                if (this.autoTrigger) {
                    //Validate HCaptcha when initiated
                    trigger();
                    registry.triggers[this.getHCaptchaId()] = new Function();
                } else {
                    registry.triggers[this.getHCaptchaId()] = trigger;
                }
                this.tokenField = null;
            }
        });
    }
);
