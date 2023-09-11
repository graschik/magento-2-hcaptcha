<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Observer;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Grasch\HCaptcha\Model\RequestHandlerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaPaypal\Observer\PayPalObserver as BasePayPalObserver;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Psr\Log\LoggerInterface;

class PayPalObserver extends BasePayPalObserver
{
    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @var RequestHandlerInterface
     */
    private RequestHandlerInterface $requestHandler;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param LoggerInterface $logger
     * @param RequestHandlerInterface $requestHandler
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        LoggerInterface $logger,
        RequestHandlerInterface $requestHandler,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
    ) {
        parent::__construct(
            $captchaResponseResolver,
            $validationConfigResolver,
            $captchaValidator,
            $actionFlag,
            $serializer,
            $isCaptchaEnabled,
            $logger
        );

        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->requestHandler = $requestHandler;
    }
    /**
     * Validates hCaptcha response.
     *
     * @param Observer $observer
     * @return void
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $key = 'paypal_payflowpro';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
        ) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();

            $this->requestHandler->setResponseType(RequestHandlerInterface::RESPONSE_TYPE_JSON);
            $this->requestHandler->execute($key, $request, $response, '');
        } else {
            parent::execute($observer);
        }
    }
}
