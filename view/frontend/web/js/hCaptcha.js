define(
[
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'Grasch_HCaptcha/js/registry',
    'Grasch_HCaptcha/js/hCaptchaScriptLoader',
    'Grasch_HCaptcha/js/nonInlineHCaptchaRenderer'
], function(Component, $, ko, _, registry, hCaptchaLoader,
    nonInlineHCaptchaRenderer) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Grasch_HCaptcha/hCaptcha',
            hCaptchaId: 'hcaptcha'
        },

        /**
         * @inheritdoc
         */
        initialize: function() {
            this._super();
            this._loadApi();
        },

        /**
         * @private
         */
        _loadApi: function() {
            if (this._isApiRegistered !== undefined) {
                if (this._isApiRegistered === true) {
                    $(window).trigger('hcaptchaapiready');
                }

                return;
            }
            this._isApiRegistered = false;

            // global function
            window.globalOnHCaptchaOnLoadCallback = function() {
                this._isApiRegistered = true;
                $(window).trigger('hcaptchaapiready');
            }.bind(this);

            hCaptchaLoader.addHCaptchaScriptTag();
        },

        initCaptcha: function() {
            var $wrapper,
                $hCaptcha,
                widgetId,
                parameters,
                $parentForm;

            if (this.captchaInitialized) {
                return;
            }

            this.captchaInitialized = true;

            $wrapper = $('#' + this.getHCaptchaId() + '-wrapper');
            $hCaptcha = $wrapper.find('.h-captcha');
            $hCaptcha.attr('id', this.getHCaptchaId());

            $parentForm = $wrapper.parents('form');

            parameters = _.extend(
                {
                    'callback': function(token) { // jscs:ignore jsDoc
                        this.hCaptchaCallback(token);
                        this.validateHCaptcha(true);
                    }.bind(this),
                    'expired-callback': function() {
                        this.validateHCaptcha(false);
                    }.bind(this),
                },
                this.settings.rendering
            );

            if (parameters.size === 'invisible' && parameters.badge !==
                'inline') {
                nonInlineHCaptchaRenderer.add($hCaptcha, parameters);
            }

            // eslint-disable-next-line no-undef
            widgetId = hcaptcha.render(this.getHCaptchaId(), parameters);
            this.initParentForm($parentForm, widgetId);

            registry.ids.push(this.getHCaptchaId());
            registry.captchaList.push(widgetId);
            registry.tokenFields.push(this.tokenField);
        },

        /**
         * Initialize parent form.
         *
         * @param {Object} parentForm
         * @param {String} widgetId
         */
        initParentForm: function(parentForm, widgetId) {
            var listeners;

            if (this.getIsInvisibleHCaptcha() && parentForm.length > 0) {
                parentForm.submit(function(event) {
                    if (!this.tokenField.value) {
                        // eslint-disable-next-line no-undef
                        hcaptcha.execute(widgetId);
                        event.preventDefault(event);
                        event.stopImmediatePropagation();
                    }
                }.bind(this));

                // Move our (last) handler topmost. We need this to avoid submit bindings with ko.
                listeners = $._data(parentForm[0], 'events').submit;
                listeners.unshift(listeners.pop());

                // Create a virtual token field
                this.tokenField = $(
                    '<input type="text" name="token" style="display: none" />')[0];
                this.$parentForm = parentForm;
                parentForm.append(this.tokenField);
            } else {
                this.tokenField = null;
            }
            if ($('#send2').length > 0) {
                $('#send2').
                    prop('disabled', false);
            }
        },

        /**
         * @param {*} state
         * @returns {jQuery}
         */
        validateHCaptcha: function(state) {
            if (!this.getIsInvisibleHCaptcha()) {
                return $(document).
                    find('input[type=checkbox].required-captcha').
                    prop('checked', state);
            }
        },

        /**
         * hCAPTCHA callback
         * @param {String} token
         */
        hCaptchaCallback: function(token) {
            if (this.getIsInvisibleHCaptcha()) {
                this.tokenField.value = token;
                this.$parentForm.submit();
            }
        },

        /**
         * Checking that hCAPTCHA is invisible type
         * @returns {Boolean}
         */
        getIsInvisibleHCaptcha: function() {
            if (this.settings ===

                void 0) {
                return false;
            }

            return this.settings.invisible;
        },

        /**
         */
        renderHCaptcha: function() {
            if (window.hcaptcha && window.hcaptcha.render) {
                this.initCaptcha();
            } else {
                $(window).on('hcaptchaapiready', function() {
                    this.initCaptcha();
                }.bind(this));
            }
        },

        /**
         * Get hCAPTCHA ID
         * @returns {String}
         */
        getHCaptchaId: function() {
            return this.hCaptchaId;
        }
    });
});
