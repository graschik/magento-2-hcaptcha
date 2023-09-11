<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Block;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\ReCaptchaUi\Block\ReCaptcha;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\UiConfigResolverInterface;

class HCaptcha extends ReCaptcha
{
    private const HCAPTCHA_TEMPLATE = 'Grasch_HCaptcha::hcaptcha.phtml';

    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @var Json
     */
    private Json $serializer;

    /**
     * @var UiConfigResolverInterface
     */
    private UiConfigResolverInterface $captchaUiConfigResolver;

    /**
     * @param Template\Context $context
     * @param UiConfigResolverInterface $captchaUiConfigResolver
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UiConfigResolverInterface $captchaUiConfigResolver,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $captchaUiConfigResolver,
            $isCaptchaEnabled,
            $serializer,
            $data
        );

        $this->captchaUiConfigResolver = $captchaUiConfigResolver;
        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout(): string
    {
        $key = $this->getData('hcaptcha_for');
        if ($this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)) {
            $layout = [
                'components' => [
                    $this->getHCaptchaId() => [
                        'settings' => $this->getCaptchaUiConfig(),
                        'component' => 'Grasch_HCaptcha/js/hCaptcha',
                        'hCaptchaId' => $this->getHCaptchaId()
                    ]
                ]
            ];

            return $this->serializer->serialize($layout);
        }

        return parent::getJsLayout();
    }

    /**
     * Get UI config for hCAPTCHA rendering
     *
     * @return array
     * @throws InputException
     */
    public function getCaptchaUiConfig(): array
    {
        $key = $this->getData('hcaptcha_for');
        $uiConfig = $this->getData('captcha_ui_config');

        if ($uiConfig) {
            $uiConfig = array_replace_recursive($this->captchaUiConfigResolver->get($key), $uiConfig);
        } else {
            $uiConfig = $this->captchaUiConfigResolver->get($key);
        }
        return $uiConfig;
    }

    /**
     * @inheritdoc
     */
    public function toHtml(): string
    {
        $key = $this->getData('hcaptcha_for');
        if ($this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)) {
            $this->setTemplate($this->getHCaptchaTemplate());
        }

        return parent::toHtml();
    }

    /**
     * Get hCAPTCHA template
     *
     * @return string
     */
    public function getHCaptchaTemplate(): string
    {
        return self::HCAPTCHA_TEMPLATE;
    }

    /**
     * Get hCAPTCHA ID
     */
    public function getHCaptchaId(): string
    {
        return (string)$this->getData('hcaptcha_id') ?: 'hcaptcha-' . sha1($this->getNameInLayout());
    }
}
