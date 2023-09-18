// jscs:disable requireDotNotation

define([
    'mage/utils/wrapper'
], function (wrapper) {
    'use strict';

    return function (jQuery) {
        jQuery.ajax = wrapper.wrapSuper(jQuery.ajax, function () {
            //Moving HCaptcha value from payload to the header for requests to web API
            var settings,
                payload;

            if (arguments.length !== 0) {
                settings = arguments.length === 1 ? arguments[0] : arguments[1];
            }

            if (settings && settings.hasOwnProperty('data')) {
                //The request has a body, trying to parse JSON data
                try {
                    payload = JSON.parse(settings.data);
                } catch (e) {
                    //Not JSON
                }
            }

            if (payload && payload.hasOwnProperty('xHCaptchaValue')) {
                if (!settings.hasOwnProperty('headers')) {
                    settings.headers = {};
                }
                settings.headers['X-HCaptcha'] = payload.xHCaptchaValue;
                delete payload['xHCaptchaValue'];
                settings.data = JSON.stringify(payload);
            }

            return this._super.apply(this, arguments);
        });

        return jQuery;
    };
});
