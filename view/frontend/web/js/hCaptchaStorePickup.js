define(['Grasch_HCaptcha/js/hCaptcha', 'uiRegistry'], function (hCaptcha, registry) {
    'use strict';

    return hCaptcha.extend({
        /**
         * @inheritdoc
         */
        renderHCaptcha: function () {
            this.captchaInitialized = false;
            this._super();
        }
    });
});
