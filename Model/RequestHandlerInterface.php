<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

interface RequestHandlerInterface extends \Magento\ReCaptchaUi\Model\RequestHandlerInterface
{
    public const RESPONSE_TYPE_DEFAULT = 'default';
    public const RESPONSE_TYPE_JSON = 'json';
    public const RESPONSE_TYPE_AUTH_EXCEPTION = 'auth_exception';

    /**
     * Set response type
     *
     * @param string $responseType
     * @return RequestHandlerInterface
     */
    public function setResponseType(string $responseType): self;

    /**
     * Get response type
     *
     * @return string
     */
    public function getResponseType(): string;
}
