<?php
/**
 * Copyright © 2015 Bakeway . All rights reserved.
 */
namespace Bakeway\Import\Block;

use Magento\Framework\UrlFactory;

class Sellerblock extends \Magento\Framework\View\Element\Template
{

    public function execute()
    {


        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        //$resultPage->setActiveMenu('Magento_TaxImportExport::system_convert_tax');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Dischem\Storeaddress\Block\Adminhtml\Import\ImportClusterCsv')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Import Store Clustering'));
        return $resultPage;
    }

}
