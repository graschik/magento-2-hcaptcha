<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Observer\Admin;

use Grasch\HCaptcha\Model\IsHCaptchaEnabledForInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;
use Magento\ReCaptchaUser\Observer\ForgotPasswordObserver as BaseForgotPasswordObserver;

class ForgotPasswordObserver extends BaseForgotPasswordObserver
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $url;

    /**
     * @var RequestHandlerInterface
     */
    private RequestHandlerInterface $requestHandler;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private IsCaptchaEnabledInterface $isCaptchaEnabled;

    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @param UrlInterface $url
     * @param RequestHandlerInterface $requestHandler
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     */
    public function __construct(
        UrlInterface $url,
        RequestHandlerInterface $requestHandler,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
    ) {
        parent::__construct(
            $url,
            $requestHandler,
            $isCaptchaEnabled
        );

        $this->url = $url;
        $this->requestHandler = $requestHandler;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
    }

    /**
     * Validates hCaptcha response.
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var Action $controller */
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();

        $key = 'user_forgot_password';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)
            && null !== $request->getParam('email')
        ) {
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->requestHandler->execute($key, $request, $response, $redirectOnFailureUrl);
        }
    }
}
