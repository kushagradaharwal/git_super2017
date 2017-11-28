<?php
/**
 * Bakeway
 *
 * @category  Bakeway
 * @package   Bakeway_Storeclone
 * @author    Bakeway
 */

namespace Bakeway\Storeclone\Plugin;

class StorelcloneImportData
{
    public function afterGetNextBunch(
        \Magento\ImportExport\Model\ResourceModel\Import\Data\Interceptor $interceptor,
        $result
    )
    {
        $entity = $interceptor->getEntityTypeCode();
        if ($entity == 'catalog_product' && is_array($result)) {
            $rowData = [];
            foreach ($result as $row) {
                if (isset($row['url_key']) && $row['url_key'] == '') {
                    $row['url_key'] = $row['name']."-".$row['sku'].uniqid(rand(), true); //random url for product
                } else {
                    $row['url_key'] = $row['name']."-".$row['sku'].uniqid(rand(), true); //random url for product
                }
                $rowData[] = $row;
            }
            return $rowData;
        }
        return $result;
    }
}