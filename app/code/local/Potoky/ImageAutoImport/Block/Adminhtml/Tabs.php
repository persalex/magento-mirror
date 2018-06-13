<?php

class Potoky_ImageAutoImport_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    private $parent;

    protected function _prepareLayout()
    {
        //get all existing tabs
        $this->parent = parent::_prepareLayout();
        //add new tab
        $this->addTab('imageautoimport', array(
            'label'     => Mage::helper('imageautoimport')->__('Image Import'),
            'url' => $this->getUrl('*/imageautoimport/flow', array('_current' => true)),
            'class' => 'ajax',
        ));
        return $this->parent;
    }
}
