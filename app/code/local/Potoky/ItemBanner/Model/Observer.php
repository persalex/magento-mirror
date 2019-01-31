<?php

class Potoky_ItemBanner_Model_Observer
{
    /**
     * Add search handles to the page groups
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addSearchHandles(Varien_Event_Observer $observer)
    {
        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = $observer->getEvent()->getObject();

        if ($widgetInstance->getType() != "itembanner/banner") {

            return $this;
        }

        $pageGroups = $widgetInstance->getData('page_groups');
        foreach ($pageGroups as &$pageGroup) {
            if (in_array('catalog_category_layered', $pageGroup['layout_handle_updates']) ||
                in_array('catalog_category_default', $pageGroup['layout_handle_updates'])) {
                $pageGroup['layout_handle_updates'][] = 'catalogsearch_result_index';
                $pageGroup['layout_handle_updates'][] = 'catalogsearch_advanced_result';
            }
        }
        unset($pageGroup);

        $widgetInstance->setData('page_groups', $pageGroups);

        return $this;
    }

    /**
     * Prepares an array used to position the banners that can be displayed for the particular request
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function prepareDisplayBanners($observer)
    {
        $layout = $observer->getLayout();
        if (!Potoky_ItemBanner_Block_Banner::$allOfTheType) {

            return $this;
        }

        /* @var $toolbar Mage_Catalog_Block_Product_List_Toolbar */
        $toolbar = $layout->getBlock('product_list_toolbar');
        if(!$toolbar) {
            return $this;
        }

        $option = Mage::getStoreConfig('cms/itembanner/rendering_type');
        $positioningArray = false;
        switch ($option) {
            case 1:
                $positioningArray = $this->renderPriorOccupy($layout, $toolbar->getCurrentMode());
                break;
            case 2:
                $positioningArray = $this->renderOccupyNext($layout, $toolbar->getCurrentMode());
                break;
        }

        if($positioningArray) {
            $count = count($positioningArray);
            $firstNum = ($toolbar->getCurrentPage() - 1) * $toolbar->getLimit() + 1;
            $lastNum = $firstNum - 1 + (int) $toolbar->getLimit();
            $previousPagesBannerQty = 0;
            $positions = array_keys($positioningArray);
            foreach ($positions as $position) {
                if($position < $firstNum) {
                    unset($positioningArray[$position]);
                    $previousPagesBannerQty++;
                }
                elseif ($position > $lastNum) {
                    unset($positioningArray[$position]);
                }
            }
            Mage::unregister('potoky_itembanner');
            Mage::register('potoky_itembanner', [
                'count'                  => $count,
                'previousPagesBannerQty' => $previousPagesBannerQty,
                'positioningArray'       => $positioningArray,
                'mode'                   => $toolbar->getCurrentMode()
            ]);
        }

        return $this;
    }

    /**
     * Calculates banners priority array when prior banner occupies the position is set in system configuratin
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderPriorOccupy($layout, $mode)
    {
        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positioningArray = [];
        $positionField = sprintf('position_in_%s', $mode);
        $maxNum = 3 * Mage::getStoreConfig(sprintf('catalog/frontend/%s_per_page', $mode));
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $blockName) {
            $block = $layout->getBlock($blockName);
            $position = $block->getData($positionField);

            if (!$block->getData('is_active')) {
                continue;
            }

            if ($position > $maxNum) {
                continue;
            }

            if ($occupyingBlockName = $positioningArray[$position]) {
                $occupyingBlockId = $layout->getBlock($occupyingBlockName)->getData('instance_id');
                $wishingBlockId = $block-> getData('instance_id');
                if($priorityArray[$occupyingBlockId] < $priorityArray[$wishingBlockId]) {
                    continue;
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return $positioningArray;
    }

    /**
     * Calculates banners priority array when occupy next position if the current one is occupied is set in system configuratin
     *
     * @param Mage_Core_Model_Layout $layout
     * @return array | boolean
     */
    private function renderOccupyNext($layout, $mode)
    {
        $positioningArray = [];
        $priorityArray = Mage::helper('itembanner')->getBannerPriorityArray();
        $positionField = sprintf('position_in_%s', $mode);
        $maxNum = 3 * Mage::getStoreConfig(sprintf('catalog/frontend/%s_per_page', $mode));
        foreach (Potoky_ItemBanner_Block_Banner::$allOfTheType as $blockName) {
            $block = $layout->getBlock($blockName);
            $position = $block->getData($positionField);

            if (!$block->getData('is_active')) {
                continue;
            }

            if ($position > $maxNum) {
                continue;
            }

            $occupyingBlockName = $positioningArray[$position];
            while ($occupyingBlockName) {
                $occupyingBlockId = $layout->getBlock($occupyingBlockName)->getData('instance_id');
                $wishingBlockId = $block-> getData('instance_id');
                if ($priorityArray[$occupyingBlockId] < $priorityArray[$wishingBlockId]) {
                    if ($position + 1 <= $maxNum) {
                        $occupyingBlockName = $positioningArray[++$position];
                        continue;
                    } else {
                        continue 2;
                    }
                } else {
                    if ($position + 1 <= $maxNum) {
                        $positioningArray[$position] = $blockName;
                        $blockName = $occupyingBlockName;
                        $block = $layout->getBlock($blockName);
                        $occupyingBlockName = $positioningArray[++$position];
                        continue;
                    } else {
                        continue 2;
                    }
                }
            }
            $positioningArray[$position] = $blockName;
        }

        return $positioningArray;
    }
}