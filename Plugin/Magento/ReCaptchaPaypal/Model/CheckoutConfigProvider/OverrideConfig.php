<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Plugin\Magento\ReCaptchaPaypal\Model\CheckoutConfigProvider;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaPaypal\Model\CheckoutConfigProvider;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;

class OverrideConfig
{
    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     */
    public function __construct(
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor,
        IsCaptchaEnabledInterface $isCaptchaEnabled
    ) {
        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
    }

    /**
     * Override config
     *
     * @param CheckoutConfigProvider $subject
     * @param array $result
     * @return array
     * @throws InputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(
        CheckoutConfigProvider $subject,
        array $result
    ): array {
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor('paypal_payflowpro')
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor('paypal_payflowpro')
        ) {
            $result = [
                'recaptcha_paypal' => false,
                'hcaptcha_paypal' => true
            ];
        }

        return $result;
    }
}
