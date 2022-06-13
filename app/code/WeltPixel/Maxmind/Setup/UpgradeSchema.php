<?php

namespace WeltPixel\Maxmind\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade schema
 * @category WeltPixel
 * @package  WeltPixel_Maxmind
 * @module   Maxmind
 * @author   WeltPixel Developer
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

//        Add Fraud Score field to sales_order table
            $tableName = $setup->getTable('sales_order');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();

                $connection->addColumn(
                    $tableName,
                    'fraud_score',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '4,2',
                        'comment' => 'Fraud Score'
                    ]
                );
            }

//        Add Fraud Score field to sales_order_grid table
            $tableName = $setup->getTable('sales_order_grid');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();

                $connection->addColumn(
                    $tableName,
                    'fraud_score',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '4,2',
                        'comment' => 'Fraud Score'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $tableName = $setup->getTable('sales_order');
            if (
                $setup->getConnection()->tableColumnExists($tableName, 'fraud_score') !== false &&
                $setup->getConnection()->tableColumnExists($tableName, 'weltpixel_fraud_score') === false
            ) {
                $setup->getConnection()->changeColumn(
                    $tableName,
                    'fraud_score',
                    'weltpixel_fraud_score',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '4,2',
                        'comment' => 'Fraud Score'
                    ]
                );
            }

            $tableName = $setup->getTable('sales_order_grid');
            if (
                $setup->getConnection()->tableColumnExists($tableName, 'fraud_score') !== false &&
                $setup->getConnection()->tableColumnExists($tableName, 'weltpixel_fraud_score') === false
            ) {
                $setup->getConnection()->changeColumn(
                    $tableName,
                    'fraud_score',
                    'weltpixel_fraud_score',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '4,2',
                        'comment' => 'Fraud Score'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $tableName = $setup->getTable('weltpixel_maxmind_data');

            $connection = $setup->getConnection();

            $connection->addColumn(
                $tableName,
                'api_version',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'default' => '1',
                    'comment' => 'Api Version'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $tableName = $setup->getTable('weltpixel_maxmind_data');

            $connection = $setup->getConnection();

            $connection->addColumn(
                $tableName,
                'chargeback_flag',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'default' => '0',
                    'comment' => 'Chargeback Flag'
                ]
            );
        }

        $setup->endSetup();
    }
}
