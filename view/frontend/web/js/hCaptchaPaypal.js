define(
    [
        'Grasch_HCaptcha/js/hCaptcha',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({

            /**
             * Hcaptcha callback
             * @param {String} token
             */
            hCaptchaCallback: function (token) {
                this.tokenField.value = token;
                this.$parentForm.trigger('hcaptcha:endExecute');
            },

            /**
             * Initialize parent form.
             *
             * @param {Object} parentForm
             * @param {String} widgetId
             */
            initParentForm: function(parentForm, widgetId) {
                var me = this;

                parentForm.on('hcaptcha:startExecute', function(event) {
                    if (!me.tokenField.value && me.getIsInvisibleHCaptcha()) {
                        // eslint-disable-next-line no-undef
                        hcaptcha.execute(widgetId);
                        event.preventDefault(event);
                        event.stopImmediatePropagation();
                    } else {
                        me.$parentForm.trigger('hcaptcha:endExecute');
                    }
                });


                // Create a virtual token field
                this.tokenField = $(
                    '<input type="text" name="token" style="display: none" />')[0];
                this.$parentForm = parentForm;
                parentForm.append(this.tokenField);
            }
        });
    }
);
