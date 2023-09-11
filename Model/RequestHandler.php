<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\ErrorMessageConfigInterface;
use Magento\ReCaptchaUi\Model\RequestHandler as BaseRequestHandler;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RequestHandler extends BaseRequestHandler implements RequestHandlerInterface
{
    /**
     * @var IsHCaptchaEnabledForInterface
     */
    private IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor;

    /**
     * @var ValidationConfigResolverInterface
     */
    private ValidationConfigResolverInterface $validationConfigResolver;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var HCaptchaResponseResolverInterface
     */
    private HCaptchaResponseResolverInterface $hCaptchaResponseResolver;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $hCaptchaValidator;

    /**
     * @var ErrorProcessor
     */
    private ErrorProcessor $errorProcessor;

    /**
     * @var string
     */
    private string $responseType = RequestHandlerInterface::RESPONSE_TYPE_DEFAULT;

    /**
     * @param IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param ValidatorInterface $hCaptchaValidator
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param LoggerInterface $logger
     * @param ErrorMessageConfigInterface $errorMessageConfig
     * @param ValidationErrorMessagesProvider $validationErrorMessagesProvider
     * @param HCaptchaResponseResolverInterface $hCaptchaResponseResolver
     * @param ErrorProcessor $errorProcessor
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        IsHCaptchaEnabledForInterface $isHCaptchaEnabledFor,
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        ValidatorInterface $hCaptchaValidator,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        LoggerInterface $logger,
        ErrorMessageConfigInterface $errorMessageConfig,
        ValidationErrorMessagesProvider $validationErrorMessagesProvider,
        HCaptchaResponseResolverInterface $hCaptchaResponseResolver,
        ErrorProcessor $errorProcessor
    ) {
        parent::__construct(
            $captchaResponseResolver,
            $validationConfigResolver,
            $captchaValidator,
            $messageManager,
            $actionFlag,
            $logger,
            $errorMessageConfig,
            $validationErrorMessagesProvider
        );

        $this->isHCaptchaEnabledFor = $isHCaptchaEnabledFor;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->logger = $logger;
        $this->hCaptchaResponseResolver = $hCaptchaResponseResolver;
        $this->hCaptchaValidator = $hCaptchaValidator;
        $this->errorProcessor = $errorProcessor;
    }

    /**
     * Validate hCAPTCHA data in request, set message and redirect if validation was failed
     *
     * @param string $key
     * @param RequestInterface $request
     * @param HttpResponseInterface $response
     * @param string $redirectOnFailureUrl
     * @return void
     * @throws InputException
     */
    public function execute(
        string $key,
        RequestInterface $request,
        HttpResponseInterface $response,
        string $redirectOnFailureUrl
    ): void {
        if (!$this->isHCaptchaEnabledFor->isHCaptchaEnabledFor($key)) {
            parent::execute($key, $request, $response, $redirectOnFailureUrl);
        } else {
            $validationConfig = $this->validationConfigResolver->get($key);

            try {
                $hCaptchaResponse = $this->hCaptchaResponseResolver->resolve($request);
            } catch (InputException $e) {
                $this->logger->error($e);
                $this->errorProcessor->processError(
                    $response,
                    ['hcaptcha_response' => $e->getMessage()],
                    $key,
                    $this->getResponseType(),
                    $redirectOnFailureUrl
                );

                $this->setResponseType(self::RESPONSE_TYPE_DEFAULT);
                return;
            }

            $validationResult = $this->hCaptchaValidator->isValid($hCaptchaResponse, $validationConfig);
            if (false === $validationResult->isValid()) {
                $this->errorProcessor->processError(
                    $response,
                    $validationResult->getErrors(),
                    $key,
                    $this->getResponseType(),
                    $redirectOnFailureUrl
                );
            }
            $this->setResponseType(self::RESPONSE_TYPE_DEFAULT);
        }
    }

    /**
     * Set response type
     *
     * @param string $responseType
     * @return RequestHandlerInterface
     */
    public function setResponseType(string $responseType): RequestHandlerInterface
    {
        $this->responseType = $responseType;

        return $this;
    }

    /**
     * Get response type
     *
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->responseType;
    }
}
