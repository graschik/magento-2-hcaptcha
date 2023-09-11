<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\CaptchaTypeResolverInterface;

class IsHCaptchaEnabledFor implements IsHCaptchaEnabledForInterface
{
    /**
     * @var CaptchaTypeResolverInterface
     */
    private CaptchaTypeResolverInterface $captchaTypeResolver;

    /**
     * @var array
     */
    private array $types;

    /**
     * @param CaptchaTypeResolverInterface $captchaTypeResolver
     * @param array $types
     */
    public function __construct(
        CaptchaTypeResolverInterface $captchaTypeResolver,
        array $types
    ) {
        $this->captchaTypeResolver = $captchaTypeResolver;
        $this->types = $types;
    }

    /**
     * @inheritdoc
     *
     * @throws InputException
     */
    public function isHCaptchaEnabledFor(string $key): bool
    {
        return in_array($this->captchaTypeResolver->getCaptchaTypeFor($key), $this->types);
    }
}
