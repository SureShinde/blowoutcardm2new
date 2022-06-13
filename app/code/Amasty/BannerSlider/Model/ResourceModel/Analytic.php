<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Analytic extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(AnalyticInterface::MAIN_TABLE, AnalyticInterface::ID);
    }
}
