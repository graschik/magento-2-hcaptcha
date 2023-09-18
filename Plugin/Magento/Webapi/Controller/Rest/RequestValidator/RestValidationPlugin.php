<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Plugin\Magento\Webapi\Controller\Rest\RequestValidator;

use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use Magento\ReCaptchaWebapiApi\Model\Data\Endpoint;
use Magento\ReCaptchaWebapiApi\Model\Data\EndpointFactory;
use Magento\ReCaptchaWebapiRest\Plugin\RestValidationPlugin as BaseRestValidationPlugin;
use Magento\Webapi\Controller\Rest\RequestValidator;
use Magento\Webapi\Controller\Rest\Router;

class RestValidationPlugin extends BaseRestValidationPlugin
{
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $hcaptchaValidator;

    /**
     * @var WebapiValidationConfigProviderInterface
     */
    private WebapiValidationConfigProviderInterface $configProvider;

    /**
     * @var RestRequest
     */
    private RestRequest $request;

    /**
     * @var Router
     */
    private Router $restRouter;

    /**
     * @var EndpointFactory
     */
    private EndpointFactory $endpointFactory;

    /**
     * @param ValidatorInterface $recaptchaValidator
     * @param WebapiValidationConfigProviderInterface $configProvider
     * @param RestRequest $request
     * @param Router $restRouter
     * @param EndpointFactory $endpointFactory
     * @param ValidatorInterface $hcaptchaValidator
     */
    public function __construct(
        ValidatorInterface $recaptchaValidator,
        WebapiValidationConfigProviderInterface $configProvider,
        RestRequest $request,
        Router $restRouter,
        EndpointFactory $endpointFactory,
        ValidatorInterface $hcaptchaValidator
    ) {
        parent::__construct(
            $recaptchaValidator,
            $configProvider,
            $request,
            $restRouter,
            $endpointFactory
        );
        $this->hcaptchaValidator = $hcaptchaValidator;
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->restRouter = $restRouter;
        $this->endpointFactory = $endpointFactory;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate(RequestValidator $subject): void
    {
        $route = $this->restRouter->match($this->request);
        /** @var Endpoint $endpoint */
        $endpoint = $this->endpointFactory->create([
            'class' => $route->getServiceClass(),
            'method' => $route->getServiceMethod(),
            'name' => $route->getRoutePath()
        ]);
        $config = $this->configProvider->getConfigFor($endpoint);

        if ($config
            && $config->getExtensionAttributes()
            && $config->getExtensionAttributes()->getHcaptcha()
        ) {
            $value = (string)$this->request->getHeader('X-HCaptcha');
            if (!$this->hcaptchaValidator->isValid($value, $config)->isValid()) {
                throw new WebapiException(__('HCaptcha validation failed, please try again'));
            }
        } else {
            parent::afterValidate($subject);
        }
    }
}
