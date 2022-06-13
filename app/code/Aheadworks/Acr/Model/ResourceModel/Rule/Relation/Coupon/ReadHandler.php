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
namespace Aheadworks\Acr\Model\ResourceModel\Rule\Relation\Coupon;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\CouponRuleInterface;
use Aheadworks\Acr\Api\Data\CouponRuleInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package Aheadworks\Acr\Model\ResourceModel\Rule\Relation\Coupon
 * @codeCoverageIgnore
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CouponRuleInterfaceFactory
     */
    private $couponRuleFactory;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param CouponRuleInterfaceFactory $couponRuleFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        CouponRuleInterfaceFactory $couponRuleFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->couponRuleFactory = $couponRuleFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_acr_rule_coupon'), ['*'])
                ->where('rule_id = :id');
            $couponRuleData = $connection->fetchRow($select, ['id' => $entityId]);

            if ($couponRuleData) {
                /** @var CouponRuleInterface $couponRuleDataObject */
                $couponRuleDataObject = $this->couponRuleFactory->create();

                $this->dataObjectHelper->populateWithArray(
                    $couponRuleDataObject,
                    $couponRuleData,
                    CouponRuleInterface::class
                );

                $entity->setCouponRule($couponRuleDataObject);
            }
        }
        return $entity;
    }
}
