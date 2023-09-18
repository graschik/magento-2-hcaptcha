<?php
declare(strict_types=1);

namespace Grasch\HCaptcha\Plugin\Magento\Framework\GraphQl\Query\ResolverInterface;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use Magento\ReCaptchaWebapiApi\Model\Data\EndpointFactory;
use Magento\ReCaptchaWebapiGraphQl\Plugin\GraphQlValidator as BaseGraphQlValidator;

class GraphQlValidator extends BaseGraphQlValidator
{
    /**
     * @var HttpRequest
     */
    private HttpRequest $request;

    /**
     * @var WebapiValidationConfigProviderInterface
     */
    private WebapiValidationConfigProviderInterface $configProvider;

    /**
     * @var EndpointFactory
     */
    private EndpointFactory $endpointFactory;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $hcaptchaValidator;

    /**
     * @param HttpRequest $request
     * @param WebapiValidationConfigProviderInterface $configProvider
     * @param ValidatorInterface $validator
     * @param EndpointFactory $endpointFactory
     * @param ValidatorInterface $hcaptchaValidator
     */
    public function __construct(
        HttpRequest $request,
        WebapiValidationConfigProviderInterface $configProvider,
        ValidatorInterface $validator,
        EndpointFactory $endpointFactory,
        ValidatorInterface $hcaptchaValidator
    ) {
        parent::__construct(
            $request,
            $configProvider,
            $validator,
            $endpointFactory
        );

        $this->request = $request;
        $this->configProvider = $configProvider;
        $this->endpointFactory = $endpointFactory;
        $this->hcaptchaValidator = $hcaptchaValidator;
    }

    /**
     * @inheritdoc
     */
    public function beforeResolve(
        ResolverInterface $subject,
        Field $fieldInfo,
        $context,
        ResolveInfo $resolveInfo
    ): void {
        if ($resolveInfo->operation->operation !== 'mutation') {
            return;
        }

        $config = $this->configProvider->getConfigFor(
            $this->endpointFactory->create([
                'class' => ltrim($fieldInfo->getResolver(), '\\'),
                'method' => 'resolve',
                'name' => $fieldInfo->getName()
            ])
        );

        if ($config
            && $config->getExtensionAttributes()
            && $config->getExtensionAttributes()->getHcaptcha()
        ) {
            $value = (string)$this->request->getHeader('X-HCaptcha');
            if (!$this->hcaptchaValidator->isValid($value, $config)->isValid()) {
                throw new GraphQlInputException(__('HCaptcha validation failed, please try again'));
            }
        } else {
            parent::beforeResolve($subject, $fieldInfo, $context, $resolveInfo);
        }
    }
}
