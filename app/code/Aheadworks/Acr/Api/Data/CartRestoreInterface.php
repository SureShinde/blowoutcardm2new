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
namespace Aheadworks\Acr\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CartRestoreInterface
 * @package Aheadworks\Acr\Api\Data
 */
interface CartRestoreInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ENTITY_ID         = 'entity_id';
    const EVENT_HISTORY_ID  = 'event_history_id';
    const RESTORE_CODE      = 'restore_code';
    /**#@-*/

    /**
     * Get cart restore ENTITY_ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set cart restore ENTITY_ID
     *
     * @param int $cartRestoreId
     * @return $this
     */
    public function setId($cartRestoreId);

    /**
     * Get Event History ID
     *
     * @return int
     */
    public function getEventHistoryId();

    /**
     * Set Event History ID
     *
     * @param int $eventHistoryId
     * @return $this
     */
    public function setEventHistoryId($eventHistoryId);

    /**
     * Get restore code
     *
     * @return string
     */
    public function getRestoreCode();

    /**
     * Set restore code
     *
     * @param string $restoreCode
     * @return $this
     */
    public function setRestoreCode($restoreCode);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\Acr\Api\Data\CartRestoreExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Acr\Api\Data\CartRestoreExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(CartRestoreExtensionInterface $extensionAttributes);
}
