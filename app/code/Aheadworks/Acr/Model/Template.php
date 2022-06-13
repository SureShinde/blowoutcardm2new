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
namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Model\Template\FilterFactory as AcrFilterFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\App\Emulation as AppEmulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\UrlInterface;
use Magento\Email\Model\Template\Config as TemplateConfig;
use Magento\Email\Model\TemplateFactory;
use Magento\Email\Model\Template\FilterFactory;

/**
 * Class Template
 * @package Aheadworks\Acr\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Template extends \Magento\Email\Model\Template
{
    /**
     * @var AcrFilterFactory
     */
    private $filterFactory;

    /**
     * @param Context $context
     * @param DesignInterface $design
     * @param Registry $registry
     * @param AppEmulation $appEmulation
     * @param StoreManagerInterface $storeManager
     * @param AssetRepository $assetRepo
     * @param Filesystem $filesystem
     * @param ScopeConfigInterface $scopeConfig
     * @param TemplateConfig $emailConfig
     * @param TemplateFactory $templateFactory
     * @param FilterManager $filterManager
     * @param UrlInterface $urlModel
     * @param FilterFactory $filterFactory
     * @param AcrFilterFactory $acrFilterFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        DesignInterface $design,
        Registry $registry,
        AppEmulation $appEmulation,
        StoreManagerInterface $storeManager,
        AssetRepository $assetRepo,
        Filesystem $filesystem,
        ScopeConfigInterface $scopeConfig,
        TemplateConfig $emailConfig,
        TemplateFactory $templateFactory,
        FilterManager $filterManager,
        UrlInterface $urlModel,
        FilterFactory $filterFactory,
        AcrFilterFactory $acrFilterFactory,
        array $data = []
    ) {
        $this->filterFactory = $acrFilterFactory;
        parent::__construct(
            $context,
            $design,
            $registry,
            $appEmulation,
            $storeManager,
            $assetRepo,
            $filesystem,
            $scopeConfig,
            $emailConfig,
            $templateFactory,
            $filterManager,
            $urlModel,
            $filterFactory,
            $data
        );
    }

    /**
     * @return AcrFilterFactory
     */
    protected function getFilterFactory()
    {
        $this->setData('is_legacy', true);
        return $this->filterFactory;
    }

    /**
     * @inheritDoc
     */
    public function load($modelId, $field = null)
    {
        parent::load($modelId, $field);
        $this->setData('is_legacy', true);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadDefault($templateId)
    {
        parent::loadDefault($templateId);
        $this->setData('is_legacy', true);

        return $this;
    }
}
