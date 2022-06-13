<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Query;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;

interface GetNewAnalyticInterface
{
    public function execute(int $bannerId, string $type): AnalyticInterface;
}
