<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\Adminhtml;

use Grasch\HCaptcha\Model\Config;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptchaUi\Model\ValidationConfigProviderInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterfaceFactory;

class ValidationConfigProvider implements ValidationConfigProviderInterface
{
    /**
     * @var RemoteAddress
     */
    private RemoteAddress $remoteAddress;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var ValidationConfigInterfaceFactory
     */
    private ValidationConfigInterfaceFactory $validationConfigFactory;

    /**
     * @param RemoteAddress $remoteAddress
     * @param Config $config
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        Config $config,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->validationConfigFactory = $validationConfigFactory;
        $this->config = $config;
        $this->config->setScope(Config::SCOPE_ADMINHTML);
    }

    /**
     * Get validation failure message
     *
     * @return string
     */
    public function getValidationFailureMessage(): string
    {
        return $this->config->getValidationFailureMessage();
    }

    /**
     * @inheritdoc
     */
    public function get(): ValidationConfigInterface
    {
        /** @var ValidationConfigInterface $validationConfig */
        $validationConfig = $this->validationConfigFactory->create(
            [
                'privateKey' => $this->config->getSecret(),
                'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                'validationFailureMessage' => $this->getValidationFailureMessage(),
            ]
        );
        return $validationConfig;
    }
}
