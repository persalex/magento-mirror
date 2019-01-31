<?php

class Potoky_ItemBanner_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties extends Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties
{
    /**
     * Form fields that need adjustment
     *
     * @var array
     */
    private $specialFields = ['parameters[goto]', 'parameters[title]', 'parameters[description]'];

    /**
     * Fieldset getter/instantiation
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function getMainFieldset()
    {
        if ($this->_getData('main_fieldset') instanceof Varien_Data_Form_Element_Fieldset) {
            return $this->_getData('main_fieldset');
        }

        $parent = parent::getMainFieldset();

        if ($this->getWidgetType() == 'itembanner/banner') {
            $parent->addType('ib_image', 'Potoky_ItemBanner_Block_Adminhtml_Widget_Helper_Image');
            $imageBlock = $this->getLayout()->createBlock('itembanner/adminhtml_widget_cropped');
            $this->setChild('itembanner_cropped', $imageBlock);
            $this->setTemplate('itembanner/form.phtml');
        }

        return $parent;
    }

    /**
     * Add field to Options form based on option configuration and adjusts $this->specialFields
     *
     * @param Varien_Object $parameter
     * @return Varien_Data_Form_Element_Abstract
     */
    protected function _addField($parameter)
    {
        if ($parent = parent::_addField($parameter)) {
            $name = $parent->getData('name');
            if (in_array($name, $this->specialFields)) {
                switch ($name) {
                    case 'parameters[goto]':
                        $parent->setData('readonly', 'readonly');
                        break;
                    case 'parameters[title]':
                        $parent->setData('maxlength', '100');
                        break;
                    case 'parameters[description]':
                        $parent->setdata('wysiwyg', true);
                        break;
                }
            }
        }

        return $parent;
    }

    /**
     * Prepare block children and data.
     * Set widget type and widget parameters if available.
     * Load TinyMce to the page for wysiwyg
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Properties
     */
    protected function _preparelayout()
    {
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return parent::_prepareLayout();
    }
}