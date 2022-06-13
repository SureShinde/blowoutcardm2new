<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('magefan_dynamic_category_rule');

        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()->newTable($tableName)
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Name'
            )
            ->addColumn(
                'description',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Description'
            )
               ->addColumn(
                   'category_ids',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   '2M',
                   [],
                   'Category Ids'
               )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'web_site',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Web Site '
            )
               ->addColumn(
                   'remove_other',
                   Table::TYPE_SMALLINT,
                   null,
                   ['nullable' => false, 'default' => '1'],
                   'Remove other products'
               )
               ->addColumn(
                   'conditions_serialized',
                   \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                   '2M',
                   [],
                   'Conditions Serialized'
               )
            ->setComment('Dynamic Category')
            ->setOption('type', 'InnoDB')
            ->setOption('charset', 'utf8');
            
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
