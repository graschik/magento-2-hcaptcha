<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Block;

class HCaptchaNewsletter extends HCaptcha
{
    private const HCAPTCHA_TEMPLATE = 'Grasch_HCaptcha::hcaptcha_newsletter.phtml';

    /**
     * @inheritdoc
     */
    public function getHCaptchaTemplate(): string
    {
        return self::HCAPTCHA_TEMPLATE;
    }
}
