<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\Frontend;

use Grasch\HCaptcha\Model\Config;
use Magento\ReCaptchaUi\Model\UiConfigProviderInterface;

class UiConfigProvider implements UiConfigProviderInterface
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Return frontend UI config for hCaptcha.
     *
     * @return array
     */
    public function get(): array
    {
        return [
            'rendering' => [
                'sitekey' => $this->config->getSiteKey(),
                'size' => $this->config->getSize(),
                'theme' => $this->config->getTheme(),
                'hl' => $this->config->getLanguageCode()
            ],
            'invisible' => false,
            'hcaptcha' => true
        ];
    }
}
