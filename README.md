<h1 align="center">grasch/magento-2-hcaptcha</h1>

<div align="center">
  <img src="https://img.shields.io/badge/magento-2.4.X-brightgreen.svg?logo=magento&longCache=true" alt="Supported Magento Versions" />
  <a href="https://GitHub.com/Naereen/StrapDown.js/graphs/commit-activity" target="_blank"><img src="https://img.shields.io/badge/maintained%3F-yes-brightgreen.svg" alt="Maintained - Yes" /></a>
</div>

## Highlight features for Magento 2 HCaptcha
- hCaptcha for your store security.
- hCaptcha works in Frontend pages.
    - Customer Login.
    - Forgot Password.
    - Create New Customer Account.
    - Edit Customer Account.
    - Contact Us.
    - Product Review.
    - Newsletter Subscription.
    - Send To Friend.
    - Checkout/Placing Order.
    - Coupon Codes.
    - PayPal PayflowPro payment form.
- hCaptcha works in Backend pages.
    - Login.
    - Forgot Password.
- Supports visible and invisible hCaptcha.
- Supports REST API and GraphQl validation.
- Compatible with Google reCaptcha. 


<img alt="Admin Login" src="docs/img/admin_login.png" width="30%"> <img alt="Login" src="docs/img/login.png" width="50%">

## How to install Magento 2 HCaptcha

### ✓ Install via composer (recommend)

Run the following commands in Magento 2 root folder:

```
composer require grasch/module-hcaptcha
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```
### ✓ Install via downloading

Download and copy files into `app/code/Grasch/HCaptcha` and run the following commands:
```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

### <span style="color:red">Important!</span>
Enable all ```Magento_ReCaptcha``` modules because ```Grasch_HCaptcha``` extension is based on these extensions.

## How to configure?
- Go to Stores -> Configuration -> Security -> Google reCAPTCHA Storefront | hCaptcha Storefront.

<img alt="Settings" src="docs/img/config.png" width="60%">

## The MIT License
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

