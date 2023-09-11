<?php
/**
 * Copyright © Grasch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace Grasch\HCaptcha\Model;

use Grasch\HCaptcha\Model\RequestMethod\Post;
use Magento\Framework\ObjectManagerInterface;
use ReCaptcha\ReCaptcha;
use ReCaptcha\ReCaptchaFactory;

class HCaptchaFactory extends ReCaptchaFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * @var Post
     */
    private Post $post;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param Post $post
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Post $post,
        $instanceName = '\\ReCaptcha\\ReCaptcha'
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->post = $post;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return ReCaptcha
     */
    public function create(array $data = []): ReCaptcha
    {
        $data['requestMethod'] = $this->post;

        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
