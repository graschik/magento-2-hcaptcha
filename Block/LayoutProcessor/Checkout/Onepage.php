<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Block\LayoutProcessor\Checkout;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

class Onepage implements LayoutProcessorInterface
{
    /**
     * @var UiConfigResolverInterface
     */
    private UiConfigResolverInterface $captchaUiConfigResolver;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     */
    public function __construct(
        UiConfigResolverInterface $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
    ) {
        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     */
    public function process($jsLayout): array
    {
        $jsLayout = $this->processCustomerLogin($jsLayout);
        $jsLayout = $this->processPaypal($jsLayout);
        $jsLayout = $this->processBraintree($jsLayout);
        $jsLayout = $this->processStorePickUp($jsLayout);
        $jsLayout = $this->processPlaceOrder($jsLayout);
        $jsLayout = $this->processCheckoutSalesRule($jsLayout);

        return $jsLayout;
    }

    /**
     * Process customer login captcha
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function processCustomerLogin(array $jsLayout): array
    {
        $key = 'customer_login';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['customer-email']['children']['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'additional-login-form-fields',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-inline-login',
                'component' => 'Grasch_HCaptcha/js/hCaptcha'
            ];
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['customer-email']['children']['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'additional-login-form-fields',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-inline-login-billing',
                'component' => 'Grasch_HCaptcha/js/hCaptcha'
            ];

            $jsLayout['components']['checkout']['children']['authentication']['children']['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'additional-login-form-fields',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-login',
                'component' => 'Grasch_HCaptcha/js/hCaptcha'
            ];

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['customer-email']['children']['recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['customer-email']['children']['recaptcha']);
            }
            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['customer-email']['children']['recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['customer-email']['children']['recaptcha']);
            }

            if (isset($jsLayout['components']['checkout']['children']['authentication']['children']['recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['authentication']['children']['recaptcha']);
            }

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['giftCardAccount']['children']
                ['gift_card_recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['afterMethods']['children']['giftCardAccount']['children']
                    ['gift_card_recaptcha']);
            }
        }

        return $jsLayout;
    }

    /**
     * Process customer paypal captcha
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function processPaypal(array $jsLayout): array
    {
        $key = 'paypal_payflowpro';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['paypal-captcha']['children']['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'additional-payment-fields',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-paypal-form',
                'component' => 'Grasch_HCaptcha/js/hCaptchaPaypal'
            ];

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children']['paypal-captcha']['children']['recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children']['paypal-captcha']['children']['recaptcha']);
            }
        }

        return $jsLayout;
    }

    /**
     * Process customer braintree captcha
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function processBraintree(array $jsLayout): array
    {
        $key = 'braintree';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['braintree-recaptcha']['children']['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'hcaptcha-braintree',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-braintree',
                'component' => 'Grasch_HCaptcha/js/hCaptchaPaypal'
            ];

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children']['paypal-captcha']['children']
                ['braintree-recaptcha']['children']['recaptcha_braintree'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']
                    ['billing-step']['children']['payment']['children']['payments-list']['children']
                    ['paypal-captcha']['children']['braintree-recaptcha']['children']['recaptcha_braintree']);
            }
        }

        return $jsLayout;
    }

    /**
     * Process Store Pick Up
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function processStorePickUp(array $jsLayout): array
    {
        $key = 'customer_login';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            $jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']
            ['store-selector']['children']['customer-email']['children']['additional-login-form-fields']['children']
            ['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'additional-login-form-fields',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'store-pickup-hcaptcha-checkout-inline-login',
                'component' => 'Grasch_HCaptcha/js/hCaptchaStorePickup'
            ];

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']
                ['store-selector']['children']['customer-email']['children']['additional-login-form-fields']['children']
                ['recaptcha'])
            ) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['store-pickup']['children']
                    ['store-selector']['children']['customer-email']['children']['additional-login-form-fields']
                    ['children']['recaptcha']);
            }
        }

        return $jsLayout;
    }

    /**
     * Process Place Order
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function processPlaceOrder(array $jsLayout): array
    {
        $key = 'place_order';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['before-place-order']['children']['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'before-place-order',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-place-order',
                'component' => 'Grasch_HCaptcha/js/hCaptchaCheckout',
                'skipPayments' => null
            ];

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children']['before-place-order']['children']
                ['place-order-recaptcha'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children']['before-place-order']
                    ['children']['place-order-recaptcha']);
            }

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['beforeMethods']['children']['place-order-recaptcha-container'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['beforeMethods']['children']['place-order-recaptcha-container']);
            }
        }

        return $jsLayout;
    }

    /**
     * Process Checkout Sales Rule
     *
     * @param array $jsLayout
     * @return array
     * @throws InputException
     */
    public function processCheckoutSalesRule(array $jsLayout): array
    {
        $key = 'coupon_code';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['afterMethods']['children']['discount']['children']
            ['hcaptcha'] = [
                'settings' => $this->captchaUiConfigResolver->get($key),
                'displayArea' => 'captcha',
                'configSource' => 'checkoutConfig',
                'hCaptchaId' => 'hcaptcha-checkout-coupon-apply',
                'component' => 'Grasch_HCaptcha/js/hCaptchaCheckoutSalesRule'
            ];

            if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['afterMethods']['children']['discount']['children']['checkout_sales_rule'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['afterMethods']['children']['discount']['children']['checkout_sales_rule']);
            }
        }

        return $jsLayout;
    }
}
