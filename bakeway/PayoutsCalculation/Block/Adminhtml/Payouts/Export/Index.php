<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_PayoutsCalculation
 * @author    Bakeway
 */

namespace Bakeway\PayoutsCalculation\Block\Adminhtml\Payouts\Export;

class Index extends \Magento\Backend\Block\Template
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }

    public function getFile() {
        $fileName = $this->getRequest()->getParam('file');
        if (isset($fileName) && $fileName != '') {
            return $fileName;
        } else {
            return false;
        }

    }
}
