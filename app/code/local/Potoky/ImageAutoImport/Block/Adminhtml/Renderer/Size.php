<?php

class Potoky_ImageAutoImport_Block_Adminhtml_Renderer_Size extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Outputs the size of the image in format like 423 kb or 1.68 mb if the size is less or greater or equal then 1Mb.
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $sizeInKb = $value / 1024;
        $imageSizeDisplay = ($sizeInKb < 1024) ? round($sizeInKb) . 'kb' : round($sizeInKb / 1024, 2) . 'mb';
        return '<span>'.$imageSizeDisplay.'</span>';

    }

}
