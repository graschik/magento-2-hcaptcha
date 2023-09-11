<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\Braintree;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\ReCaptchaAdminUi\Model\OptionSource as BaseOptionSource;

class OptionSource implements OptionSourceInterface
{
    /**
     * @var BaseOptionSource
     */
    private BaseOptionSource $optionSource;

    /**
     * @param BaseOptionSource $optionSource
     */
    public function __construct(
        BaseOptionSource $optionSource
    ) {
        $this->optionSource = $optionSource;
    }

    /**
     * Disable hcaptcha for braintree
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = $this->optionSource->toOptionArray();
        $result = [];

        foreach ($options as $option) {
            if (isset($option['value']) && in_array($option['value'], ['hcaptcha', 'hcaptcha_invisible'])) {
                continue;
            }
            $result[] = $option;
        }

        return $result;
    }
}
