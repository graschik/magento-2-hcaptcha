define([
    'jquery'
], function ($) {
    'use strict';

    var hCaptchaEntities = [],
        initialized = false,
        rendererHcaptchaId = 'hcaptcha-invisible',
        rendererHCaptcha = null;

    return {
        /**
         * Add hCaptcha entity to checklist.
         *
         * @param {jQuery} hCaptchaEntity
         * @param {Object} parameters
         */
        add: function (hCaptchaEntity, parameters) {
            if (!initialized) {
                this.init();
                hcaptcha.render(rendererHcaptchaId, parameters);
                setInterval(this.resolveVisibility, 100);
                initialized = true;
            }

            hCaptchaEntities.push(hCaptchaEntity);
        },

        /**
         * Show additional hCaptcha instance if any other should be visible, otherwise hide it.
         */
        resolveVisibility: function () {
            hCaptchaEntities.some(function (entity) {
                return entity.is(':visible') &&
                    // 900 is some magic z-index value of modal popups.
                    (entity.closest('[data-role=\'modal\']').length === 0 || entity.zIndex() > 900);
            }) ? rendererHCaptcha.show() : rendererHCaptcha.hide();
        },

        /**
         * Initialize additional hCaptcha instance.
         */
        init: function () {
            rendererHCaptcha = $('<div/>', {
                'id': rendererHcaptchaId
            });
            rendererHCaptcha.hide();
            $('body').append(rendererHCaptcha);
        }
    };
});
