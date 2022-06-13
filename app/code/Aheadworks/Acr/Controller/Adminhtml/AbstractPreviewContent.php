<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Acr
 * @version    1.1.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Acr\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use Aheadworks\Acr\Model\Preview\Storage;

/**
 * Class AbstractPreviewContent
 * @package Aheadworks\Acr\Controller\Adminhtml
 */
abstract class AbstractPreviewContent extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::rules';

    /**
     * {@inheritdoc}
     */
    const LAYOUT = 'aw_acr_preview';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Validator $formKeyValidator
     * @param UrlInterface $urlBuilder
     * @param Storage $storage
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Validator $formKeyValidator,
        UrlInterface $urlBuilder,
        Storage $storage
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->urlBuilder = $urlBuilder;
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $result = [
            'error'     => true,
            'message'   => __('Unknown error occured!')
        ];
        if ($this->getRequest()->isAjax()) {
            $data = $this->getRequest()->getPostValue();
            if ($data && $this->formKeyValidator->validate($this->getRequest())) {
                if (isset($data['email_data'])) {
                    $this->storage->saveEmailData($data['email_data']);
                }
                $result = [
                    'error'     => false,
                    'message'   => __('Success.'),
                    'url'   => $this->getPreviewUrl(isset($data['email_data']['id']) ? $data['email_data']['id'] : null)
                ];
            } else {
                $this->_forward('noroute');
            }
        }
        return $resultJson->setData($result);
    }

    /**
     * Get url for preview
     *
     * @param null|int $id
     */
    abstract public function getPreviewUrl($id = null);
}
