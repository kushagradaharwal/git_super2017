<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Cities
 * @author    Bakeway
 */

namespace Bakeway\Cities\Model\ResourceModel\Cities;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as DbAbstractCollection;

/**
 * ResourceModel Cities data collection
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
        $this->_init('Bakeway\Cities\Model\Cities', 'Bakeway\Cities\Model\ResourceModel\Cities');
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
