<?php

class Potoky_Surprise_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $this->addTabAfter('surprise', array(
                'label' => Mage::helper('catalog')->__('Surprise Products'),
                'url' => $this->getUrl('*/*/surprise', array('_current' => true)),
                'class' => 'ajax',
            ), 'related');
        }
    }
}