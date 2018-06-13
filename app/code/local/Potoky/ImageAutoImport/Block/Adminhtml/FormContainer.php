<?php

/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 4/17/2018
 * Time: 1:03 PM
 */
class Potoky_ImageAutoImport_Block_Adminhtml_Formcontainer extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'imageautoimport';
        $this->_controller = 'adminhtml';
        $this->_mode = 'importtoqueue';

        parent::__construct();
        $this->removeButton('reset')
            ->_updateButton('back', 'onclick', 'window.location.href=\''.$this->getUrl('*/catalog_product/').'\'')
            ->_updateButton('save', 'label', Mage::Helper('imageautoimport')->__('Check & Go'))
            ->_updateButton('save', 'id', 'upload_button')
            ->_updateButton('save', 'onclick', 'postToQueue();');
    }

    public function getHeaderText()
    {
        return Mage::helper('imageautoimport')->__('Add to Queue');
    }
}
