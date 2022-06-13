<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


declare(strict_types=1);

namespace Amasty\Shopby\Model\Layer\Filter;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Exception\StateException;
use Amasty\Shopby\Model\Layer\Filter\Traits\FromToDecimal;
use Amasty\Shopby\Model\Source\DisplayMode;
use Amasty\Shopby\Api\Data\FromToFilterInterface;

class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price implements FromToFilterInterface
{
    const NUMBERS_AFTER_POINT = 4;
    const AM_BASE_PRICE = 'am_base_price';
    const DELTA_FOR_BORDERS_RANGE = 0.0049;

    use FromToDecimal;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    private $settingHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var string
     */
    private $currencySymbol;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    private $dataProvider;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Registry|null
     */
    private $coreRegistry = null;

    /**
     * @var \Magento\Search\Api\SearchInterface
     */
    private $search;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Search\Api\SearchInterface $search,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->settingHelper = $settingHelper;
        $this->currencySymbol = $priceCurrency->getCurrencySymbol();
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
        $this->shopbyRequest = $shopbyRequest;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->search = $search;
        $this->messageManager = $messageManager;
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );
    }

    /**
     * @return array
     */
    public function getFromToConfig()
    {
        return $this->getConfig('price');
    }

    /**
     * @return array
     */
    protected function _getItemsData()
    {
        if ($this->isHide()) {
            return [];
        }

        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $facets = $this->getFacetedData();

        $data = [];
        if (count($facets) > 1) { // two range minimum
            foreach ($facets as $key => $aggregation) {
                $count = $aggregation['count'];
                if (strpos($key, '_') === false) {
                    continue;
                }
                $data[] = $this->prepareData($key, $count);
            }
        }

        if (count($this->getFromToConfig()) && count($data) == 1) {
            $data = [];
        }

        return $data;
    }

    /**
     * @param string $key
     * @param int $count
     * @return array
     */
    private function prepareData($key, $count)
    {
        [$from, $to] = explode('_', $key);
        if ($from == '*') {
            $from = $this->getFrom($to);
        }
        if ($to == '*') {
            $to = '';
        }

        $label = $this->renderRangeLabel(
            empty($from) ? 0 : $from,
            $to ? $to - 0.01 : $to
        );

        $from = $from ? round($from * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT) : $from;
        $to = $to ? round($to * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT) : $to;

        $value = $from . '-' . $to . $this->dataProvider->getAdditionalRequestData();
        $data = [
            'label' => $label,
            'value' => $value,
            'count' => $count,
            'from' => $from,
            'to' => $to,
        ];

        return $data;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->isApplied()) {
            return $this;
        }

        $filter = $this->shopbyRequest->getFilterParam($this);
        $noValidate = 0;
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        $isSlider = $filterSetting->getDisplayMode() == DisplayMode::MODE_SLIDER;
        $newValue = '';

        if (!empty($filter) && is_string($filter)) {
            $copyFilter = $filter;
            $filter = explode('-', $filter);

            $toValue = isset($filter[1]) && $filter[1] ? $filter[1] : '';
            $filter = $filter[0] . '-' . $toValue;
            $validateFilter = $this->dataProvider->validateFilter($copyFilter);

            $values = explode('-', $copyFilter);
            $displayMode = $filterSetting->getDisplayMode();
            $includeBorders = $this->isSliderOrFromTo($displayMode) ? self::DELTA_FOR_BORDERS_RANGE : 0;

            //apply delta
            $values[0] = isset($values[0]) && $values[0] ? (floatval($values[0]) - $includeBorders) : '';
            $values[1] = isset($values[1]) && $values[1] ? (floatval($values[1]) + $includeBorders) : '';

            //apply rate
            $values[0] = $values[0] ? floatval($values[0]) / $this->getCurrencyRate() : '';
            $values[1] = $values[1] ? floatval($values[1]) / $this->getCurrencyRate() : '';
            $newValue = $values[0] . '-' . $values[1];

            if (!$validateFilter) {
                $noValidate = 1;
            } else {
                $this->setFromTo($validateFilter[0], $validateFilter[1]);
            }
        }

        $request->setParam($this->getRequestVar(), $newValue ?: $filter);
        $request->setPostValue(self::AM_BASE_PRICE, isset($copyFilter) ? $copyFilter : $filter);

        $apply = parent::apply($request);

        if ($noValidate) {
            return $this;
        }

        if (!empty($filter) && !is_array($filter)) {
            if ($isSlider) {
                $this->getLayer()->getProductCollection()->addFieldToFilter('price', $filter);
            }

            //TODO was removed part with group options(unused as i think). check it and delete comment
        }

        return $apply;
    }

    /**
     * @return mixed
     */
    private function getFacetedData()
    {
        $key = 'price_facets' . $this->_requestVar;
        if ($this->coreRegistry->registry($key) === null) {
            $productCollection = $this->getLayer()->getProductCollection();
            try {
                $facets = $productCollection->getFacetedData(
                    $this->getAttributeModel()->getAttributeCode(),
                    $this->getSearchResult()
                );
            } catch (StateException $e) {
                if (!$this->messageManager->hasMessages()) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Make sure that "%1" attribute can be used in layered navigation',
                            $this->getAttributeModel()->getAttributeCode()
                        )
                    );
                }
                $facets = [];
            }

            $this->coreRegistry->register($key, $facets);
        }

        return $this->coreRegistry->registry($key);
    }

    /**
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return string|\Magento\Framework\Phrase
     */
    protected function renderRangeLabel($fromPrice, $toPrice)
    {
        $fromPrice = round($fromPrice * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT);
        if (!$toPrice) {
            $toPrice = 0;
        }
        if ($this->getCurrencyRate() != 1.0) {
            $toPrice = round($toPrice * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT);
        }

        return $this->renderLabelDependOnPrice((float) $fromPrice, (float) $toPrice);
    }

    /**
     * method is used for Amasty\GroupedOptions\Plugin\Shopby\Model\Layer\Filter\Price plugin
     * @param float $fromPrice
     * @param float $toPrice
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function renderLabelDependOnPrice(float $fromPrice, float $toPrice)
    {
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if (!$toPrice) {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }
}