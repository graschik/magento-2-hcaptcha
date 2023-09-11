<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Psr\Log\LoggerInterface;

class ErrorProcessor
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var ActionFlag
     */
    private ActionFlag $actionFlag;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var ValidationErrorMessagesProvider
     */
    private ValidationErrorMessagesProvider $validationErrorMessagesProvider;

    /**
     * @param SerializerInterface $serializer
     * @param ActionFlag $actionFlag
     * @param Config $config
     * @param ManagerInterface $messageManager
     * @param LoggerInterface $logger
     * @param ValidationErrorMessagesProvider $validationErrorMessagesProvider
     */
    public function __construct(
        SerializerInterface $serializer,
        ActionFlag $actionFlag,
        Config $config,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        ValidationErrorMessagesProvider $validationErrorMessagesProvider
    ) {
        $this->serializer = $serializer;
        $this->actionFlag = $actionFlag;
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider;
    }

    /**
     * Process error
     *
     * @param ResponseInterface $response
     * @param array $errorMessages
     * @param string $sourceKey
     * @param string $responseType
     * @param string $redirectOnFailureUrl
     * @return void
     * @throws AuthenticationException
     */
    public function processError(
        ResponseInterface $response,
        array $errorMessages,
        string $sourceKey,
        string $responseType,
        string $redirectOnFailureUrl
    ): void {
        $validationErrorText = $this->config->getValidationFailureMessage();
        $technicalErrorText = $this->config->getTechnicalFailureMessage();

        $message = $errorMessages ? $validationErrorText : $technicalErrorText;

        foreach ($errorMessages as $errorMessageCode => $errorMessageText) {
            if (!$this->isValidationError($errorMessageCode)) {
                $message = $technicalErrorText;
                $this->logger->error(
                    __(
                        'hCAPTCHA \'%1\' form error: %2',
                        $sourceKey,
                        $errorMessageText
                    )
                );
            }
        }

        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);

        $this->processResponse($response, $responseType, $message, $redirectOnFailureUrl);
    }

    /**
     * Process response
     *
     * @param ResponseInterface $response
     * @param string $responseType
     * @param string $message
     * @param string $redirectOnFailureUrl
     * @return ResponseInterface
     * @throws AuthenticationException
     */
    private function processResponse(
        ResponseInterface $response,
        string $responseType,
        string $message,
        string $redirectOnFailureUrl
    ): ResponseInterface {
        switch ($responseType) {
            case RequestHandlerInterface::RESPONSE_TYPE_JSON:
                $jsonPayload = $this->serializer->serialize([
                    'errors' => true,
                    'message' => $message,
                ]);
                $response->representJson($jsonPayload);
                break;
            case RequestHandlerInterface::RESPONSE_TYPE_DEFAULT:
                $this->messageManager->addErrorMessage($message);
                $response->setRedirect($redirectOnFailureUrl);
                break;
            case RequestHandlerInterface::RESPONSE_TYPE_AUTH_EXCEPTION:
                throw new AuthenticationException(__($message));
        }

        return $response;
    }

    /**
     * Check if error code present in validation errors list.
     *
     * @param string $errorMessageCode
     * @return bool
     */
    private function isValidationError(string $errorMessageCode): bool
    {
        return $errorMessageCode !== $this->validationErrorMessagesProvider->getErrorMessage($errorMessageCode);
    }
}
