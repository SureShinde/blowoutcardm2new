<?php

namespace WeltPixel\Maxmind\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Install schema
 * @category WeltPixel
 * @package  WeltPixel_Maxmind
 * @module   Maxmind
 * @author   WeltPixel Developer
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        // Get weltpixel_maxmind_data table
        $tableName = $installer->getTable('weltpixel_maxmind_data');

        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            /*
             * Create table weltpixel_maxmind_data
             */
            $table = $installer->getConnection()->newTable(
                $tableName
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Order ID'
            )->addColumn(
                'fraud_score',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '6,2',
                ['nullable' => false, 'default' => '0'],
                'Fraud Score'
            )->addColumn(
                'fraud_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false, 'default' => ''],
                'Fraud Data'
            )->addColumn(
                'sent_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false, 'default' => ''],
                'Sent Data'
            )->addIndex(
                $installer->getIdxName('weltpixel_maxmind_data', ['id']),
                ['id']
            )->addIndex(
                $installer->getIdxName('weltpixel_maxmind_data', ['order_id']),
                ['order_id']
            )->addIndex(
                $installer->getIdxName('weltpixel_maxmind_data', ['fraud_score']),
                ['fraud_score']
            );

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
