<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model\RequestMethod;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Serialize\SerializerInterface;
use ReCaptcha\ReCaptcha;
use ReCaptcha\RequestMethod;
use ReCaptcha\RequestParameters;

class Post implements RequestMethod
{
    private const SITE_VERIFY_URL = 'https://api.hcaptcha.com/siteverify';

    /**
     * @var Client
     */
    private Client $http;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param Client $http
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Client $http,
        SerializerInterface $serializer
    ) {
        $this->http = $http;
        $this->serializer = $serializer;
    }

    /**
     * Submit the POST request with the specified parameters.
     *
     * @param RequestParameters $params Request parameters
     * @return string Body of the hCAPTCHA response
     */
    public function submit(RequestParameters $params): string
    {
        try {
            $response = $this->http->request(
                'POST',
                self::SITE_VERIFY_URL,
                [
                    'form_params' => $params->toArray()
                ]
            );
            return $response->getBody()->getContents();
        } catch (\Throwable $exception) {
            return $this->serializer->serialize([
                'success' => false,
                'error-codes' => [
                    ReCaptcha::E_CONNECTION_FAILED
                ]
            ]);
        }
    }
}
