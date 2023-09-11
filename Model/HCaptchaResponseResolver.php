<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;

class HCaptchaResponseResolver implements HCaptchaResponseResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(RequestInterface $request): string
    {
        $reCaptchaParam = $request->getParam(self::PARAM_HCAPTCHA);
        if (empty($reCaptchaParam)) {
            throw new InputException(__('Can not resolve hCAPTCHA parameter.'));
        }
        return $reCaptchaParam;
    }
}
