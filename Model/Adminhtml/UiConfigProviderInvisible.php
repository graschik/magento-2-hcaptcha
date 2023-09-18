<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\Adminhtml;

use Grasch\HCaptcha\Model\Config;
use Magento\ReCaptchaUi\Model\UiConfigProviderInterface;

class UiConfigProviderInvisible implements UiConfigProviderInterface
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
        $this->config->setScope(Config::SCOPE_ADMINHTML);
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
                'sitekey' => $this->config->getSiteKey(Config::TYPE_INVISIBLE),
                'size' => 'invisible',
                'theme' => $this->config->getTheme(Config::TYPE_INVISIBLE),
                'hl' => $this->config->getLanguageCode(Config::TYPE_INVISIBLE)
            ],
            'invisible' => true,
            'hcaptcha' => true
        ];
    }
}
