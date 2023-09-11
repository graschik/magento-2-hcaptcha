<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Magento\Framework\App\PlainTextRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;

interface HCaptchaResponseResolverInterface
{
    /**
     * Parameter name for hCAPTCHA response
     */
    public const PARAM_HCAPTCHA = 'h-captcha-response';

    /**
     * Extract hCAPTCHA response parameter from Request object
     *
     * @param RequestInterface|PlainTextRequestInterface $request
     * @return string
     * @throws InputException
     */
    public function resolve(RequestInterface $request): string;
}
