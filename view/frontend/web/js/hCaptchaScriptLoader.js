define([], function () {
    'use strict';

    var scriptTagAdded = false;

    return {
        /**
         * Add script tag. Script tag should be added once
         */
        addHCaptchaScriptTag: function () {
            var element, scriptTag;

            if (!scriptTagAdded) {
                element = document.createElement('script');
                scriptTag = document.getElementsByTagName('script')[0];

                element.async = true;
                element.src = 'https://js.hcaptcha.com/1/api.js' +
                    '?onload=globalOnHCaptchaOnLoadCallback&render=explicit&recaptchacompat=off';

                scriptTag.parentNode.insertBefore(element, scriptTag);
                scriptTagAdded = true;
            }
        }
    };
});
