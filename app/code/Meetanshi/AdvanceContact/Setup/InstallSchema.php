<?php

namespace Meetanshi\AdvanceContact\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        try {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('meetanshi_contact_department')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Contact Department Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                null,
                [],
                'E-mail'
            )->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Is Active'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->setComment(
                'Contact Department'
            );

            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('meetanshi_contact_department'),
                $setup->getIdxName(
                    $installer->getTable('meetanshi_contact_department'),
                    [ 'name', 'email'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name', 'email'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $tableAdvanceContact = $installer->getConnection()->newTable(
                $installer->getTable('meetanshi_advance_contact')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Contact Department Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                null,
                [],
                'E-mail'
            )->addColumn(
                'comment',
                Table::TYPE_TEXT,
                null,
                [],
                'Comment'
            )->addColumn(
                'department',
                Table::TYPE_TEXT,
                null,
                [],
                'Department'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )->setComment(
                'Contact Department'
            );

            $installer->getConnection()->createTable($tableAdvanceContact);

            $installer->getConnection()->addIndex(
                $installer->getTable('meetanshi_advance_contact'),
                $setup->getIdxName(
                    $installer->getTable('meetanshi_advance_contact'),
                    [ 'name', 'email'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name', 'email'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $installer->endSetup();

        } catch (\Zend_Db_Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }
}
