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
namespace Aheadworks\Acr\Model;

use Aheadworks\Acr\Model\Sample\Reader\Xml as XmlReader;

/**
 * Class Sample
 * @package Aheadworks\Acr\Model
 */
class Sample
{
    /**
     * @var  XmlReader
     */
    private $xmlReader;

    /**
     * @param XmlReader $reader
     */
    public function __construct(
        XmlReader $reader
    ) {
        $this->xmlReader = $reader;
    }

    /**
     * Get sample data
     *
     * @return array
     */
    public function get()
    {
        $data = $this->xmlReader->read();
        return $data;
    }
}
