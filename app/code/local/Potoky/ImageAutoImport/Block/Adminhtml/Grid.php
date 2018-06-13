<?php

class Potoky_ImageAutoImport_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('flow_grid');
        $this->setDefaultSort('created_at');
        $this->setUseAjax(true);
    }

    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _prepareCollection()
    {
        $product = $this->_getProduct();
        $sku = $product->getSku();
        $collection = Mage::getModel('imageautoimport/imageinfo')
            ->getCollection()
            ->addFieldToFilter('product_sku', array('eq' => $sku));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => Mage::Helper('imageautoimport')->__('Created at'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'created_at'
        ));

        $this->addColumn('loading_at', array(
            'header'    => Mage::Helper('imageautoimport')->__('Loading at'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'loading_at'
        ));

        $this->addColumn('url', array(
            'header'    => Mage::Helper('imageautoimport')->__('Image URL'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'image_url'
        ));

        $this->addColumn('image_size', array(
            'header'    => Mage::Helper('imageautoimport')->__('Image Size'),
            'sortable'  => true,
            'width'     => 60,
            'renderer' => 'Potoky_ImageAutoImport_Block_Adminhtml_Renderer_Size',
            'index'     => 'image_size'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::Helper('imageautoimport')->__('Loading Status'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'status'
        ));

        $this->addColumn('error_message', array(
            'header'    => Mage::Helper('imageautoimport')->__('Error Message'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'error_message'
        ));

    }

    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/imageautoimport/flowgrid', array('_current'=>true));
    }
}
