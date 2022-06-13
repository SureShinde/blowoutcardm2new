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
namespace Aheadworks\Acr\Model\ResourceModel\Rule\Relation\Store;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Acr\Model\ResourceModel\Rule\Relation\Store
 * @codeCoverageIgnore
 */
class SaveHandler implements ExtensionInterface
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
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $storeIds = $entity->getStoreIds();
        $storeIdsOrig = $this->getStoreIds($entityId);

        $toInsert = array_diff($storeIds, $storeIdsOrig);
        $toDelete = array_diff($storeIdsOrig, $storeIds);

        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName('aw_acr_rule_store');

        if ($toInsert) {
            $data = [];
            foreach ($toInsert as $storeId) {
                $data[] = [
                    'rule_id' => (int)$entityId,
                    'store_id' => (int)$storeId,
                ];
            }
            $connection->insertMultiple($tableName, $data);
        }
        if (count($toDelete)) {
            $connection->delete(
                $tableName,
                ['rule_id = ?' => $entityId, 'store_id IN (?)' => $toDelete]
            );
        }
        return $entity;
    }

    /**
     * Get store IDs to which entity is assigned
     *
     * @param int $entityId
     * @return array
     */
    private function getStoreIds($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('aw_acr_rule_store'), 'store_id')
            ->where('rule_id = :id');
        return $connection->fetchCol($select, ['id' => $entityId]);
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnectionName()
        );
    }
}
