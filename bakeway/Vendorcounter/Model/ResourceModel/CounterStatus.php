<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Vendorcounter
 * @author    Bakeway
 */

namespace Bakeway\Vendorcounter\Model\ResourceModel;

class CounterStatus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct()
    {
        $this->_init('bakeway_counter', 'id');
    }

    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        } return parent::load($object, $value, $field);
    }

    /**
     * Get counter identifier by sku vendorId and date
     *
     * @param string $sku
     * @param int $vendorId
     * @param datetime $currentDate
     * @return int|false
     */
    public function getCounterId($sku, $vendorId, $currentDate)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from('bakeway_counter', 'id')->where('sku = :sku')->where('seller_id = :vendorId')->where('date = :currentDate');

        $bind = [':sku' => (string)$sku,':vendorId'=>$vendorId,':currentDate'=>$currentDate];

        return $connection->fetchOne($select, $bind);
    }
}
