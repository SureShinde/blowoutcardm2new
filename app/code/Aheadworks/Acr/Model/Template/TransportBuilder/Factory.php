<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Acr
 * @version    1.1.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Acr\Model\Template\TransportBuilder;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Aheadworks\Acr\Model\Template\TransportBuilderInterface;

/**
 * Class Factory
 *
 * @package Aheadworks\Acr\Model\Template\TransportBuilder
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->objectManager = $objectManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Create email transport builder instance
     *
     * @return TransportBuilderInterface
     */
    public function create()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $messageClassName = version_compare($magentoVersion, '2.3.3', '>=')
            ? Version233::class
            : VersionPriorTo233::class;

        return $this->objectManager->create($messageClassName);
    }
}
