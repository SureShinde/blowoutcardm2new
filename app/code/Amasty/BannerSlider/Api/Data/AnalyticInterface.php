<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Api\Data;

interface AnalyticInterface
{
    const MAIN_TABLE = 'amasty_bannerslider_banner_analytics';

    const ID = 'id';
    const TYPE = 'type';
    const COUNTER = 'counter';
    const BANNER_ID = 'banner_id';
    const VERSION_ID = 'version_id';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\BannerSlider\Api\Data\AnalyticInterface
     */
    public function setId($id);

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string $type
     * @return void
     */
    public function setType(string $type): void;

    /**
     * @return int
     */
    public function getCounter();

    /**
     * @param int $counter
     * @return void
     */
    public function setCounter(int $counter): void;

    /**
     * @return int|null
     */
    public function getBannerId(): ?int;

    /**
     * @param int $bannerId
     * @return void
     */
    public function setBannerId(int $bannerId): void;

    /**
     * @return int|null
     */
    public function getVersionId(): ?int;

    /**
     * @param int $versionId
     * @return void
     */
    public function setVersionId(int $versionId): void;
}
