<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */
namespace Bakeway\Storeclone\Block\Adminhtml\Customer\Edit\Tab\Grid\Renderer;

use Magento\Framework\DataObject;

class SellerAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * get action url
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $html = '<a href="javascript:void(0)" class="storerow" data-param="'.$row->getSellerId().'">select</a>';
        return $html;
    }
}
