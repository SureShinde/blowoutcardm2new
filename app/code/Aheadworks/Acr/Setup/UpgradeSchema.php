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
namespace Aheadworks\Acr\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Aheadworks\Acr\Setup
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCartRestoreTable($setup);
        }
    }

    /**
     * Add CartRestore table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addCartRestoreTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_acr_cart_restore'
         */
        $cartRestoreTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_acr_cart_restore')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Cart Restore Id'
        )->addColumn(
            'event_history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Event History Id'
        )->addColumn(
            'restore_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Restore Code'
        )->addIndex(
            $setup->getIdxName('aw_acr_cart_restore', ['entity_id', 'restore_code']),
            ['entity_id', 'restore_code']
        )->addForeignKey(
            $setup->getFkName('aw_acr_cart_restore', 'event_history_id', 'aw_acr_cart_history', 'id'),
            'event_history_id',
            $setup->getTable('aw_acr_cart_history'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($cartRestoreTable);

        return $this;
    }
}
