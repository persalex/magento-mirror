<?php


class Potoky_Surprise_Helper_Catalog_Category extends Mage_Catalog_Helper_Category
{
    public function __construct()
    {
        Mage::log(__CLASS__);
    }

    public function canShow($category)
    {
        if (1) {
            return parent::canShow($category);
        } else {
            return false;
        }
    }
}