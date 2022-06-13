<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_BannerSlider
 */


declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Temp;

use Amasty\BannerSlider\Model\ResourceModel\Analytic\AddActionInTempTable as AddActionInTempTableResource;
use Exception;
use Psr\Log\LoggerInterface;

class AddActionInTempTable
{
    /**
     * @var AddActionInTempTableResource
     */
    private $addActionInTempTableResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        AddActionInTempTableResource $addActionInTempTableResource,
        LoggerInterface $logger
    ) {
        $this->addActionInTempTableResource = $addActionInTempTableResource;
        $this->logger = $logger;
    }

    public function execute(string $type, array $actions): void
    {
        try {
            $this->addActionInTempTableResource->execute($type, $actions);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
