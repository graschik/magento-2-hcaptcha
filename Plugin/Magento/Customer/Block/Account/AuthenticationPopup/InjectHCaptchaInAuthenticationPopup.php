<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Plugin\Magento\Customer\Block\Account\AuthenticationPopup;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

class InjectHCaptchaInAuthenticationPopup
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var UiConfigResolverInterface
     */
    private UiConfigResolverInterface $uiConfigResolver;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @param SerializerInterface $serializer
     * @param UiConfigResolverInterface $uiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     */
    public function __construct(
        SerializerInterface $serializer,
        UiConfigResolverInterface $uiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
    ) {
        $this->serializer = $serializer;
        $this->uiConfigResolver = $uiConfigResolver;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
    }

    /**
     * Inject hcaptcha
     *
     * @param AuthenticationPopup $subject
     * @param string $result
     * @return string
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(
        AuthenticationPopup $subject,
        string $result
    ): string {
        $key = 'customer_login';
        if (!$this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && !$this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            return $result;
        }

        $layout = $this->serializer->unserialize($result);
        if (isset($layout['components']['authenticationPopup']['children']['recaptcha'])) {
            unset($layout['components']['authenticationPopup']['children']['recaptcha']);
        }

        $layout['components']['authenticationPopup']['children']['hcaptcha'] = [
            'settings' => $this->uiConfigResolver->get($key),
            'displayArea' => 'additional-login-form-fields',
            'configSource' => 'checkoutConfig',
            'hCaptchaId' => 'hcaptcha-checkout-inline-login',
            'component' => 'Grasch_HCaptcha/js/hCaptcha'
        ];

        return $this->serializer->serialize($layout);
    }
}
