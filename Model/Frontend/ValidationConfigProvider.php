<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\Frontend;

use Grasch\HCaptcha\Model\Config;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptchaUi\Model\ValidationConfigProviderInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterfaceFactory;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigExtensionFactory;

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
     * @var ValidationConfigExtensionFactory
     */
    private ValidationConfigExtensionFactory $validationConfigExtensionFactory;

    /**
     * @param RemoteAddress $remoteAddress
     * @param Config $config
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     * @param ValidationConfigExtensionFactory $validationConfigExtensionFactory
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        Config $config,
        ValidationConfigInterfaceFactory $validationConfigFactory,
        ValidationConfigExtensionFactory $validationConfigExtensionFactory
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
        $this->validationConfigFactory = $validationConfigFactory;
        $this->validationConfigExtensionFactory = $validationConfigExtensionFactory;
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
        $extensionAttributes = $this->validationConfigExtensionFactory->create();
        $extensionAttributes->setData('hcaptcha', true);

        /** @var ValidationConfigInterface $validationConfig */
        $validationConfig = $this->validationConfigFactory->create(
            [
                'privateKey' => $this->config->getSecret(),
                'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                'validationFailureMessage' => $this->getValidationFailureMessage(),
                'extensionAttributes' => $extensionAttributes,
            ]
        );
        return $validationConfig;
    }
}
