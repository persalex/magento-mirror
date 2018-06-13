<?php


class Potoky_Surprise_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    public function __construct()
    {
        Mage::log(__CLASS__);

        Mage_Catalog_Model_Product::__construct();
    }

    public function getSurpriseProducts()
    {
        if (!$this->hasSurpriseProducts()) {
            $products = array();
            $collection = $this->getSurpriseProductCollection();
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setSurpriseProducts($products);
        }
        return $this->getData('surprise_products');
    }

    public function getSurpriseProductIds()
    {
        if (!$this->hasSurpriseProductIds()) {
            $ids = array();
            foreach ($this->getSurpriseProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setSurpriseProductIds($ids);
        }
        return $this->getData('surprise_product_ids');
    }

    public function getSurpriseProductCollection()
    {
        $collection = $this->getLinkInstance()->useSurpriseLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    public function getSurpriseLinkCollection()
    {
        $collection = $this->getLinkInstance()->useSurpriseLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    public function addCustomOption($code, $value, $product=null)
    {
        $product = $product ? $product : $this;
        $option = Mage::getModel('catalog/product_configuration_item_option')
            ->addData(array(
                'product_id'=> $product->getId(),
                'product'   => $product,
                'code'      => $code,
                'value'     => $value,
            ));
        $this->_customOptions[$code] = $option;
        return $this;
    }
}