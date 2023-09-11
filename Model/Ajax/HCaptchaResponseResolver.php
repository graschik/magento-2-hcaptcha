<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\Ajax;

use Grasch\HCaptcha\Model\HCaptchaResponseResolverInterface;
use Magento\Framework\App\PlainTextRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\SerializerInterface;

class HCaptchaResponseResolver implements HCaptchaResponseResolverInterface
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     *
     * @param RequestInterface|PlainTextRequestInterface $request
     * @return string
     * @throws InputException
     */
    public function resolve(RequestInterface $request): string
    {
        $content = $request->getContent();
        if (empty($content)) {
            throw new InputException(__('Can not resolve hCAPTCHA response.'));
        }

        try {
            $jsonParams = $this->serializer->unserialize($content);
        } catch (\InvalidArgumentException $e) {
            throw new InputException(__('Can not resolve hCAPTCHA response.'), $e);
        }

        if (empty($jsonParams[self::PARAM_HCAPTCHA])) {
            throw new InputException(__('Can not resolve hCAPTCHA response.'));
        }
        return $jsonParams[self::PARAM_HCAPTCHA];
    }
}
