<?php


class Potoky_Surprise_Model_Catalog_Product_Link extends Mage_Catalog_Model_Product_Link
{
    const LINK_TYPE_SURPRISE   = 6;

    public function useSurpriseLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_SURPRISE);
        return $this;
    }

    public function saveProductRelations($product)
    {
        $data = $product->getSurpriseLinkData();
        $surp_ids = array_keys($data);
        $parent_id = (int) $product->getId();
        $newdata = [];
        $model = Mage::getModel('potoky_surprise/surprise');
        foreach ($surp_ids as $id) {
            $newdata['product_id'] = $parent_id;
            $newdata['linked_product_id'] = (int) $id;
            $model->setData($newdata);
            try {
                $model->save();
            }
            catch (Exception $e) {
                if ($e->getCode() == 23000);
                continue;
            }
        }
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_SURPRISE);
        }
        parent::saveProductRelations($product);
    }
}