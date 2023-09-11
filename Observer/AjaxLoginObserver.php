<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Observer;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Grasch\HCaptcha\Model\RequestHandlerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaCustomer\Model\AjaxLogin\ErrorProcessor;
use Magento\ReCaptchaCustomer\Observer\AjaxLoginObserver as BaseAjaxLoginObserver;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Psr\Log\LoggerInterface;

class AjaxLoginObserver extends BaseAjaxLoginObserver
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
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param LoggerInterface $logger
     * @param ErrorProcessor $errorProcessor
     * @param RequestHandlerInterface $requestHandler
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        LoggerInterface $logger,
        ErrorProcessor $errorProcessor,
        RequestHandlerInterface $requestHandler,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
    ) {
        parent::__construct(
            $captchaResponseResolver,
            $validationConfigResolver,
            $captchaValidator,
            $isCaptchaEnabled,
            $logger,
            $errorProcessor
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
        $key = 'customer_login';
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
