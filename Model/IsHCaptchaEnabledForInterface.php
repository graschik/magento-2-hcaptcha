<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

interface IsHCaptchaEnabledForInterface
{
    /**
     * Return true if hCAPTCHA is enabled for specific functionality
     *
     * @param string $key
     * @return bool
     */
    public function isHCaptchaEnabledFor(string $key): bool;
}
