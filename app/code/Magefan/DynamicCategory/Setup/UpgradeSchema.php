<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package Magefan\DynamicCategory\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {

            $tableName = $setup->getTable('magefan_dynamic_category_rule');

            if ($setup->getConnection()->isTableExists($tableName) == true) {

                $connection = $setup->getConnection();

                $connection
                    ->addColumn($tableName, 'remove_products_from_other_categories', [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Remove Products from Other Categories',
                    'default' => '0']);

                $connection->addColumn($tableName, 'priority', [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Rule priority executing'
                ]);


            }
        }

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $tableName = $setup->getTable('magefan_dynamic_category_rule');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->dropColumn($tableName, 'web_site');
                $connection->addColumn(
                    $tableName,
                    'website_ids',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => '32',
                        'default' => '0',
                        'comment' => 'Website Id\'s'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $tableName = $setup->getTable('magefan_dynamic_category_rule');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection->addColumn($tableName, 'catalog_price_rule_ids', [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Catalog Price Rules Id\'s'
                ]);
            }
        }


        $setup->endSetup();
    }
}
