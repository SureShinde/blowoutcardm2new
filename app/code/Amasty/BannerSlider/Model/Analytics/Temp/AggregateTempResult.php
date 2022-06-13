<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Temp;

use Magento\Framework\Exception\InputException;

class AggregateTempResult
{
    /**
     * @var int
     */
    private $counter;

    /**
     * @var int
     */
    private $versionId;

    public function __construct(array $data)
    {
        if (!isset($data[TempEntity::AGGREGATE_VERSION]) || !isset($data[TempEntity::AGGREGATE_COUNTER])) {
            throw new InputException(__('Can\'t create AggregateTempResult. Invalid data given.'));
        }

        $this->counter = (int) $data[TempEntity::AGGREGATE_COUNTER];
        $this->versionId = (int) $data[TempEntity::AGGREGATE_VERSION];
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function getVersionId(): int
    {
        return $this->versionId;
    }
}
