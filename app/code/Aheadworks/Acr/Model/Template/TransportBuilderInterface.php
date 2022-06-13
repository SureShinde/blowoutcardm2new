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
namespace Aheadworks\Acr\Model\Template;

/**
 * Interface TransportBuilderInterface
 *
 * @package Aheadworks\Acr\Model\Template
 */
interface TransportBuilderInterface
{
    /**
     * Set template identifier
     *
     * @param string $templateIdentifier
     * @return $this
     */
    public function setTemplateIdentifier($templateIdentifier);

    /**
     * Set template options
     *
     * @param array $templateOptions
     * @return $this
     */
    public function setTemplateOptions($templateOptions);

    /**
     * Set template vars
     *
     * @param array $templateVars
     * @return $this
     */
    public function setTemplateVars($templateVars);

    /**
     * Set mail from address
     *
     * @param string|array $from
     * @return $this
     */
    public function setFrom($from);

    /**
     * Add to address
     *
     * @param array|string $address
     * @param string $name
     * @return $this
     */
    public function addTo($address, $name = '');

    /**
     * Get mail transport
     *
     * @return \Magento\Framework\Mail\TransportInterface
     */
    public function getTransport();
}
