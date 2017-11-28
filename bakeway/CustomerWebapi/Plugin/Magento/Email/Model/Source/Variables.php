<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_CustomerWebapi
 * @author    Bakeway
 */
namespace Bakeway\CustomerWebapi\Plugin\Magento\Email\Model\Source;

class Variables
{

    public function afterGetData(\Magento\Email\Model\Source\Variables $subject, $result)
    {
        $result = array_merge($result, [
            [
                'value' => 'react_site_settings/react_settings_general/react_url',
                'label' => __('Support Phone Number')
            ],
        ]);
        
        
        $result = array_merge($result, [
            [
                'value' => 'react_site_settings/react_settings_general/guest_track_url',
                'label' => __('Guest Order Track Url')
            ],
        ]);
        

        return $result;
    }
}