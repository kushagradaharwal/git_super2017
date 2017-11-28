<?php
namespace Bakeway\Deliveryrangeprice\Block\Adminhtml;
class Rangeprice extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_controller = 'adminhtml_rangeprice';/*block grid.php directory*/
        $this->_blockGroup = 'Bakeway_Deliveryrangeprice';
        $this->_headerText = __('Rangeprice');
        $this->_addButtonLabel = __('Add New Entry');
        parent::_construct();

    }
}
