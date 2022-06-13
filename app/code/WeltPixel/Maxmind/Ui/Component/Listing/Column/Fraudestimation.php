<?php

namespace WeltPixel\Maxmind\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\Order;
use Magento\Ui\Component\Listing\Columns\Column;
use WeltPixel\Maxmind\Helper\Data as MaxmindHelper;

/**
 * Class Fraudestimation
 * @package WeltPixel\Maxmind\Ui\Component\Listing\Column
 */
class Fraudestimation extends Column
{
    /**
     * @var Order
     */
    private $_order;

    /**
     * @var MaxmindHelper|null
     */
    private $_helper = null;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Order $order
     * @param MaxmindHelper $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        Order $order,
        MaxmindHelper $helper,

        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->_order = $order;
        $this->_helper = $helper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = '';

        $entityId = array_key_exists('entity_id', $item) ? $item['entity_id'] : null;
        if ($entityId) {
            $order = $this->_order->load($entityId);

            if ($order->getId()) {
                $fraudScore = $order->getData('weltpixel_fraud_score');
                $scoreThreshold = $this->_helper->getConfigValue('general/score_threshold', $order->getStoreId());

                $color = $scoreThreshold && $fraudScore && ($fraudScore >= $scoreThreshold) ? 'red' : 'auto';

                $content = $fraudScore && $fraudScore > 0
                    ? '<span style="color:' . $color . '">' . $fraudScore . '%</span>'
                    : '-';

                return $content;
            }
        }

        return $content;
    }
}
