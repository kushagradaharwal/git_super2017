<?php
/**
 * Copyright © 2015 Bakeway . All rights reserved.
 */
namespace Bakeway\Import\Block\Adminhtml\Import;
class Index extends \Magento\Backend\Block\Template
{

  /**
     * @var string
     */

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }
	
	
}
