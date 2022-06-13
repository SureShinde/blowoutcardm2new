<?php

namespace WeltPixel\Maxmind\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * @package WeltPixel\Maxmind\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $maxmindDataTable = $setup->getTable('weltpixel_maxmind_data');
            try {
                $setup->getConnection()->query("UPDATE " . $maxmindDataTable . " SET api_version = 1 WHERE 1");
            } catch (\Exception $ex) {
            }
        }

        $installer->endSetup();
    }
}
