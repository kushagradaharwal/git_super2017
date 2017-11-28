<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Model\ResourceModel\CounterStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 *
 * Vendorcounter Resource Model Collection\AbstractCollection
 */
class Collection extends AbstractCollection {

    /**
     * Initialize Resource Collection
     *
     * @return void
     */
    public function _construct() {
        $this->_init('Bakeway\Vendorcounter\Model\CounterStatus', 'Bakeway\Vendorcounter\Model\ResourceModel\CounterStatus');
    }

    /**
     * Retrieve clear select
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);

        return $select;
    }

    /**
     * Retrieve all sku for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllSkus($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('sku');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}
