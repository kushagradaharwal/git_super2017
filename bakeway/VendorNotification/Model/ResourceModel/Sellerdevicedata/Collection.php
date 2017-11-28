<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_VendorNotification
 * @author    Bakeway
 */

namespace Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;

/**
 * ResourceModel Seller device data collection
 */
class Collection extends DbAbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bakeway\VendorNotification\Model\Sellerdevicedata', 'Bakeway\VendorNotification\Model\ResourceModel\Sellerdevicedata');
    }


    /**
     * Add field filter to collection.
     *
     * @param array|string          $field
     * @param string|int|array|null $condition
     *
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }
}
