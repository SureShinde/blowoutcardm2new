<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Banner\Grid;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection as BannerCollection;
use Magento\Framework\Api\Search\SearchResultInterface;
use Zend_Db_Expr;

class Collection extends BannerCollection implements SearchResultInterface
{
    /**
     * @var array
     */
    private $mappedFields = [
        'id' => 'main_table.id'
    ];

    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface
     */
    protected $aggregations;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $resourceModel = \Amasty\BannerSlider\Model\ResourceModel\Banner::class,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_init($model, $resourceModel);
    }

    /**
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     * @return void
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @param array|null $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function beforeAddLoadedItem(\Magento\Framework\DataObject $item)
    {
        return $item;
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->addDynamicTable();
        $this->addAnalytics();

        parent::_renderFiltersBefore();
    }

    private function addAnalytics(): void
    {
        $analyticTable = $this->getConnection()->select()->from(
            ['banner' => $this->getTable(BannerInterface::STATIC_TABLE_NAME)],
            [
                'banner_id' => BannerInterface::ID,
                'ctr' => new Zend_Db_Expr('ROUND(click.counter/view.counter*100, 2)')
            ]
        )->joinLeft(
            ['view' => $this->getTable(AnalyticInterface::MAIN_TABLE)],
            'banner.id = view.banner_id and view.type=\'view\'',
            ['view' => 'counter']
        )->joinLeft(
            ['click' => $this->getTable(AnalyticInterface::MAIN_TABLE)],
            'banner.id = click.banner_id and click.type=\'click\'',
            ['click' => 'counter']
        );

        $this->getSelect()->joinLeft(
            ['analytic' => $analyticTable],
            'main_table.id = analytic.banner_id',
            [
                'view' => $this->getConnection()->getIfNullSql('view', 0),
                'click' => $this->getConnection()->getIfNullSql('click', 0),
                'ctr' => $this->getConnection()->getIfNullSql('ctr', 0)
            ]
        );
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return BannerCollection
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (array_key_exists($field, $this->mappedFields)) {
            $field = $this->mappedFields[$field];
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return BannerCollection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (array_key_exists($field, $this->mappedFields)) {
            $field = $this->mappedFields[$field];
        }
        return parent::setOrder($field, $direction); // TODO: Change the autogenerated stub
    }
}
