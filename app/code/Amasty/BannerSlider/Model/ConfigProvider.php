<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Magento\Framework\Data\CollectionDataSourceInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract implements CollectionDataSourceInterface
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_bannerslider/';
}
