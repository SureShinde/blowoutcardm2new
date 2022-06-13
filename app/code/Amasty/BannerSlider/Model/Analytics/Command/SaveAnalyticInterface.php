<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Command;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Model\Analytics\Analytic;
use Magento\Framework\Exception\CouldNotSaveException;

interface SaveAnalyticInterface
{
    /**
     * @param AnalyticInterface|Analytic $analytic
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(AnalyticInterface $analytic): void;
}
