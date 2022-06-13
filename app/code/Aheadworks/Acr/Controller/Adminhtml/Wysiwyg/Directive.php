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
namespace Aheadworks\Acr\Controller\Adminhtml\Wysiwyg;

use Magento\Backend\App\Action;
use Magento\Cms\Model\Template\Filter;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Url\DecoderInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Image\AdapterFactory;

/**
 * Class Directive
 * @package Aheadworks\Acr\Controller\Adminhtml\Wysiwyg
 */
class Directive extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Cms::media_gallery';

    /**
     * @var DecoderInterface
     */
    private $urlDecoder;

    /**
     * @var RawFactory
     */
    private $resultRawFactory;

    /**
     * @var AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @param Action\Context $context
     * @param DecoderInterface $urlDecoder
     * @param RawFactory $resultRawFactory
     * @param AdapterFactory $adapterFactory
     * @param Config $config
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        DecoderInterface $urlDecoder,
        RawFactory $resultRawFactory,
        AdapterFactory $adapterFactory,
        Config $config,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->urlDecoder = $urlDecoder;
        $this->resultRawFactory = $resultRawFactory;
        $this->adapterFactory = $adapterFactory;
        $this->config = $config;
        $this->filter = $filter;
    }

    /**
     * Template directives callback
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = $this->urlDecoder->decode($directive);
        try {
            $imagePath = $this->filter->filter($directive);
            $resultRaw = $this->getRaw($imagePath);
        } catch (\Exception $e) {
            $imagePath = $this->config->getSkinImagePlaceholderPath();
            $resultRaw = $this->getRaw($imagePath);
        }
        return $resultRaw;
    }

    /**
     * Retrieve raw by path
     *
     * @param $imagePath
     * @return \Magento\Framework\Controller\Result\Raw
     */
    private function getRaw($imagePath)
    {
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $image */
        $image = $this->adapterFactory->create();
        $image->open($imagePath);
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setHeader('Content-Type', $image->getMimeType());
        $resultRaw->setContents($image->getImage());

        return $resultRaw;
    }
}
