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
namespace Aheadworks\Acr\Model\Sample;

use Magento\Framework\Module\Dir\Reader as DirReader;

/**
 * Class SchemaLocator
 * @package Aheadworks\Acr\Model\Sample
 */
class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    /**
     * @var string
     */
    private $schema;

    /**
     * @var string
     */
    private $perFileSchema;

    /**
     * SchemaLocator constructor.
     * @param DirReader $moduleReader
     */
    public function __construct(
        DirReader $moduleReader
    ) {
        $this->schema = $moduleReader->getModuleDir('etc', 'Aheadworks_Acr') . '/' . 'sample_data.xsd';
        $this->perFileSchema = $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
