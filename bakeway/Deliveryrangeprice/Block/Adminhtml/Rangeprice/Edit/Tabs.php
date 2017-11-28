<?php
namespace Bakeway\Deliveryrangeprice\Block\Adminhtml\Rangeprice\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {

        parent::_construct();
        $this->setId('checkmodule_rangeprice_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Rangeprice Information'));
    }
}