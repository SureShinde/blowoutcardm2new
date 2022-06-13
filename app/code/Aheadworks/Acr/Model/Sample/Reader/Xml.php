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
namespace Aheadworks\Acr\Model\Sample\Reader;

use Aheadworks\Acr\Model\Sample\Converter\Xml as XmlConverter;
use Aheadworks\Acr\Model\Sample\SchemaLocator as SampleSchemaLocator;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Config\Dom as ConfigDom;

/**
 * Class Xml
 * @package Aheadworks\Acr\Model\Sample\Reader
 */
class Xml extends \Magento\Framework\Config\Reader\Filesystem
{
    /**
     * @param FileResolverInterface $fileResolver
     * @param XmlConverter $converter
     * @param SampleSchemaLocator $schemaLocator
     * @param ValidationStateInterface $validationState
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     */
    public function __construct(
        FileResolverInterface $fileResolver,
        XmlConverter $converter,
        SampleSchemaLocator $schemaLocator,
        ValidationStateInterface $validationState,
        $fileName = 'sample_data.xml',
        $idAttributes = [],
        $domDocumentClass = ConfigDom::class,
        $defaultScope = 'global'
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }
}
