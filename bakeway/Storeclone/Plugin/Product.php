<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Plugin;

class Product
{
    public function beforeValidateRow(
        \Magento\CatalogImportExport\Model\Import\Product\Interceptor $interceptor,
        array $rowData,
        $rowNum)
    {
        if (isset($rowData['url_key']) && $rowData['url_key'] == '') {
            $rowData['url_key'] = $rowData['name']."-".$rowData['sku'].uniqid(rand(), true); //random url for product
        } else {
            $rowData['url_key'] = $rowData['name']."-".$rowData['sku'].uniqid(rand(), true); //random url for product
        }
        return [$rowData, $rowNum];
    }
}