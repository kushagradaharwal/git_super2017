<?php

namespace Bakeway\Shopapi\Model\ResourceModel;

class CustomShop extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct() {
        $this->_init('vendor_shop', 'id');
    }

    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null) {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        } return parent::load($object, $value, $field);
    }

}
