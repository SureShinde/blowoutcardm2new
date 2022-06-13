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
namespace Aheadworks\Acr\Model\ResourceModel;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Queue
 * @package Aheadworks\Acr\Model\ResourceModel
 */
class Queue extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_acr_queue', 'id');
    }

    /**
     * Delete queue items by cart history id
     *
     * @param int $cartHistoryId
     * @return $this
     */
    public function deleteItemsByCartHistory($cartHistoryId)
    {
        $writeAdapter = $this->getConnection();
        $conditions = sprintf(
            'cart_history_id=%s AND status<>%s',
            $writeAdapter->quote($cartHistoryId),
            $writeAdapter->quote(QueueInterface::STATUS_SENT)
        );
        $writeAdapter->delete($this->getMainTable(), $conditions);

        return $this;
    }
}
