define([], function () {
    'use strict';

    return {
        /**
         * hcaptchaId: token map.
         *
         * Tokens for already verified hcaptcha.
         */
        tokens: {},

        /**
         * hcaptchaId: triggerFn map.
         *
         * Call a trigger to initiate a hcaptcha verification.
         */
        triggers: {},

        /**
         * hcaptchaId: callback map
         */
        _listeners: {},

        /**
         * hcaptchaId: bool map
         */
        _isInvisibleType: {},

        /**
         * Add a listener to when the HCaptcha finishes verification
         * @param {String} id - HCaptchaId
         * @param {Function} func - Will be called back with the token
         */
        addListener: function (id, func) {
            if (this.tokens.hasOwnProperty(id)) {
                func(this.tokens[id]);
            } else {
                this._listeners[id] = func;
            }
        },

        /**
         * Remove a listener
         *
         * @param id
         */
        removeListener: function (id) {
            this._listeners[id] = undefined;
        }
    };
});
