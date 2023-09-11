<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Observer\Admin;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Grasch\HCaptcha\Model\RequestHandlerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaUser\Observer\LoginObserver as BaseLoginObserver;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Psr\Log\LoggerInterface;

class LoginObserver extends BaseLoginObserver
{
    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @var RequestHandlerInterface
     */
    private RequestHandlerInterface $requestHandler;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var string
     */
    private string $loginActionName;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     * @param RequestHandlerInterface $requestHandler
     * @param ResponseFactory $responseFactory
     * @param string $loginActionName
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        RequestInterface $request,
        LoggerInterface $logger,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor,
        RequestHandlerInterface $requestHandler,
        ResponseFactory $responseFactory,
        string $loginActionName
    ) {
        parent::__construct(
            $captchaResponseResolver,
            $validationConfigResolver,
            $captchaValidator,
            $isCaptchaEnabled,
            $request,
            $logger,
            $loginActionName
        );
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
        $this->requestHandler = $requestHandler;
        $this->request = $request;
        $this->responseFactory = $responseFactory;
        $this->loginActionName = $loginActionName;
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
        $key = 'user_login';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
            && $this->request->getFullActionName() === $this->loginActionName
        ) {
            $response = $this->responseFactory->create();
            $this->requestHandler->setResponseType(RequestHandlerInterface::RESPONSE_TYPE_AUTH_EXCEPTION);
            $this->requestHandler->execute($key, $this->request, $response, '');
        } else {
            parent::execute($observer);
        }
    }
}
