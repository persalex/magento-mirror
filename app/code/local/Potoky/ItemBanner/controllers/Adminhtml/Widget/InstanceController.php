<?php

require_once(
    Mage::getModuleDir('controllers','Mage_Widget') .
    DS . 'Adminhtml' .
    DS . 'Widget' .
    DS . 'InstanceController.php');
class Potoky_ItemBanner_Adminhtml_Widget_InstanceController extends Mage_Widget_Adminhtml_Widget_InstanceController
{
    /**
     * Prepare widget parameters and validate them if the instance tends to be set as active
     *
     * @return array
     */
    protected function _prepareParameters()
    {
        $parent = parent::_prepareParameters();

        $currentWidgetInstance = Mage::registry('current_widget_instance');
        if ($currentWidgetInstance &&
            $currentWidgetInstance->getType() == 'itembanner/banner') {
            if ($parent['image']['delete']) {
                $parent['image'] = null;
                $parent['rel_coords_grid'] = Mage::helper('core')->jsonEncode([]);
                $parent['rel_coords_list'] = Mage::helper('core')->jsonEncode([]);
            }
            elseif ($uploaded = $this->imageUpload()) {
                $parent['image'] = $uploaded;
                $parent['rel_coords_grid'] = Mage::helper('core')->jsonEncode([]);
                $parent['rel_coords_list'] = Mage::helper('core')->jsonEncode([]);
            }
            elseif ($image = $currentWidgetInstance->getWidgetParameters()['image']) {
                $parent['image'] = $image;

                if ($this->getRequest()->getPost('ib_grid_change_status')) {
                    $this->cropFromRelCoords($image, $parent['rel_coords_grid'], 'grid');
                };

                if ($this->getRequest()->getPost('ib_list_change_status')) {
                    $this->cropFromRelCoords($image, $parent['rel_coords_list'], 'list');
                }
            }

            $parent['description'] = str_replace('&nbsp;', '', $parent['description']);

            if ($parent['is_active'] == 1) {
                try {
                    $this->validateActivationEligibility($parent);
                } catch (Exception $e) {
                    $parent['is_active'] = 0;
                }
            }
        }

        $parent['instance_id'] = $currentWidgetInstance->getId();

        return $parent;
    }

    /**
     * Upload image for the instance
     * 
     * @return bool | string
     */
    private function imageUpload()
    {
        $path = Mage::getBaseDir('media') . DS . 'itembanner' . DS;
        try {
            $uploader = new Mage_Core_Model_File_Uploader('parameters[image]');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->addValidateCallback(
                Potoky_ItemBanner_Model_File_Validator_Image::NAME,
                new Potoky_ItemBanner_Model_File_Validator_Image(),
                "validate"
            );
            $result = $uploader->save($path);

        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }

            return false;
        }

        return $result['file'];
    }

    /**
     * Crop from original image and save a new one from cropping
     *
     * @param $baseImageFile
     * @param $relCoords
     * @param $mode
     * 
     * @return void
     */
    private function cropFromRelCoords($baseImageFile, $relCoords, $mode)
    {
        $relCoords = Mage::helper('core')->jsonDecode($relCoords);
        extract($relCoords);

        $image =  new Varien_Image(Mage::helper('itembanner')->getImageUri(
            $baseImageFile,
            '',
            false)
        );
        $modeImageFile = Mage::helper('itembanner')->getImageUri($baseImageFile, $mode, false);
        $image->crop(
            $relCoords[1] * $image->getOriginalHeight(),
            $relCoords[0] * $image->getOriginalWidth(),
            (1 - $relCoords[2]) * $image->getOriginalWidth(),
            (1 - $relCoords[3]) * $image->getOriginalHeight()
        );
        $image->save($modeImageFile);
    }

    /**
     * Validate parameters of the widget instance for being eligible to be set as active
     *
     * @param $parameters
     *
     * @return void
     * @throws Exception
     */
    private function validateActivationEligibility($parameters)
    {
        $errorMessage =  Mage::helper('itembanner')->getErrorMessage('starting_message');
        $errorPresent = false;

        if (!filter_var($parameters['position_in_grid'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ])) {
            $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('position')[0];
            $errorPresent = true;
        }

        if (!filter_var($parameters['position_in_list'], FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ])) {
            $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('position')[1];
            $errorPresent = true;
        }

        if ($parameters['image'] && !$errorPresent['delete']) {

            if (empty(Mage::helper('core')->jsonDecode($parameters['rel_coords_grid']))) {
                $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('rel_coords')[0];
                $errorPresent = true;
            }

            if (empty(Mage::helper('core')->jsonDecode($parameters['rel_coords_list']))) {
                $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('rel_coords')[1];
                $errorPresent = true;
            }

        }
        else {
            $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('image')[0];
            $errorPresent = true;
        }

        if (empty($parameters['title']) || strlen($parameters['title']) > 100) {
            $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('title');
            $errorPresent = true;
        }

        if (empty($parameters['description']) || strlen(strip_tags($parameters['description'])) > 300) {
            $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('description');
            $errorPresent = true;
        }

        if (!filter_var($parameters['link'], FILTER_VALIDATE_URL)) {
            $errorMessage .= '<br>' . Mage::helper('itembanner')->getErrorMessage('link');
            $errorPresent = true;
        }

        if ($errorPresent) {
            Mage::throwException(trim($errorMessage, ',') . '.', 'adminhtml/session');
        }
    }
}
