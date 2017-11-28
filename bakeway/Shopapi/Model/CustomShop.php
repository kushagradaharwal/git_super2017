<?php

namespace Bakeway\Shopapi\Model;

use Magento\Framework\Model\AbstractModel;
use Bakeway\Shopapi\Api\Data\ShopStatusInterface;

class CustomShop extends AbstractModel implements ShopStatusInterface {

    const STATUS_OPEN = '1';
    const STATUS_CLOSE = '0';
    const STATUS_OPEN_STRING = "open";
    const STATUS_CLOSE_STRING = "close";

    protected function _construct() {
        $this->_init('Bakeway\Shopapi\Model\ResourceModel\CustomShop');
    }

    public function load($id, $field = null) {
        if ($id === null) {
            return $this->noRoute();
        }

        return parent::load($id, $field);
    }

    /**
     * @return object
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getStatus() {
        $status = parent::getData(self::STATUS);
        if ($status == self::STATUS_OPEN) {
            return self::STATUS_OPEN_STRING;
        } else {
            return self::STATUS_CLOSE_STRING;
        }
    }

    /**
     * @param int $status
     * @return int
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function setStatus($status) {
        $this->setData(self::STATUS, $status);
    }

}
