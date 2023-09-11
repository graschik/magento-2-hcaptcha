<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const TYPE_DEFAULT = 'h_captcha';
    public const TYPE_INVISIBLE = 'h_captcha_invisible';

    private const TYPES_CONFIG = [
        self::TYPE_INVISIBLE => [
            'site_key' => self::XML_PATH_SITE_KEY_INVISIBLE,
            'secret' => self::XML_PATH_SECRET_INVISIBLE,
            'theme' => self::XML_PATH_THEME_INVISIBLE,
            'lang' => self::XML_PATH_LANGUAGE_INVISIBLE
        ],
        self::TYPE_DEFAULT => [
            'site_key' => self::XML_PATH_SITE_KEY,
            'secret' => self::XML_PATH_SECRET,
            'theme' => self::XML_PATH_THEME,
            'size' => self::XML_PATH_SIZE,
            'lang' => self::XML_PATH_LANGUAGE
        ],
    ];
    private const XML_PATH_SITE_KEY = 'recaptcha_#scope#/h_captcha/site_key';
    private const XML_PATH_SECRET = 'recaptcha_#scope#/h_captcha/secret_key';
    private const XML_PATH_SIZE = 'recaptcha_#scope#/h_captcha/size';
    private const XML_PATH_THEME = 'recaptcha_#scope#/h_captcha/theme';
    private const XML_PATH_LANGUAGE = 'recaptcha_#scope#/h_captcha/lang';
    private const XML_PATH_SITE_KEY_INVISIBLE = 'recaptcha_#scope#/h_captcha_invisible/site_key';
    private const XML_PATH_SECRET_INVISIBLE = 'recaptcha_#scope#/h_captcha_invisible/secret_key';
    private const XML_PATH_THEME_INVISIBLE = 'recaptcha_#scope#/h_captcha_invisible/theme';
    private const XML_PATH_LANGUAGE_INVISIBLE = 'recaptcha_#scope#/h_captcha_invisible/lang';
    private const XML_PATH_VALIDATION_FAILURE_MESSAGE =
        'recaptcha_#scope#/h_captcha_failure_messages/validation_failure_message';
    private const XML_PATH_TECHNICAL_FAILURE_MESSAGE =
        'recaptcha_#scope#/h_captcha_failure_messages/technical_failure_message';

    public const SCOPE_FRONTEND = 'frontend';
    public const SCOPE_ADMINHTML = 'backend';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var string
     */
    private string $scope = self::SCOPE_FRONTEND;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * Set config scope
     *
     * @param string $scope
     * @return $this
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get site key
     *
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getSiteKey(
        string $type = self::TYPE_DEFAULT,
        int $storeId = null
    ): string {
        $value = (string)$this->scopeConfig->getValue(
            $this->processPath(self::TYPES_CONFIG[$type]['site_key']),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->encryptor->decrypt($value);
    }

    /**
     * Get secret
     *
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getSecret(
        string $type = self::TYPE_DEFAULT,
        int $storeId = null
    ): string {
        $value = (string)$this->scopeConfig->getValue(
            $this->processPath(self::TYPES_CONFIG[$type]['secret']),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $this->encryptor->decrypt($value);
    }

    /**
     * Get theme
     *
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getTheme(
        string $type = self::TYPE_DEFAULT,
        int $storeId = null
    ): string {
        return (string)$this->scopeConfig->getValue(
            $this->processPath(self::TYPES_CONFIG[$type]['theme']),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get language code
     *
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getLanguageCode(
        string $type = self::TYPE_DEFAULT,
        int $storeId = null
    ): string {
        return (string)$this->scopeConfig->getValue(
            $this->processPath(self::TYPES_CONFIG[$type]['lang']),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get size
     *
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getSize(
        string $type = self::TYPE_DEFAULT,
        int $storeId = null
    ): string {
        return (string)$this->scopeConfig->getValue(
            $this->processPath(self::TYPES_CONFIG[$type]['size']),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get validation failure message
     *
     * @param int|null $storeId
     * @return string
     */
    public function getValidationFailureMessage(int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            $this->processPath(self::XML_PATH_VALIDATION_FAILURE_MESSAGE),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get technical failure message
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTechnicalFailureMessage(int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            $this->processPath(self::XML_PATH_TECHNICAL_FAILURE_MESSAGE),
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Process config path
     *
     * @param string $path
     * @return string
     */
    private function processPath(string $path): string
    {
        return str_replace('#scope#', $this->scope, $path);
    }
}
