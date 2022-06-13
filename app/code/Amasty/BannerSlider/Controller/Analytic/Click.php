<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Analytic;

use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;

class Click extends Ctr
{
    protected function getType(): string
    {
        return TempEntity::CLICK_TYPE;
    }
}
