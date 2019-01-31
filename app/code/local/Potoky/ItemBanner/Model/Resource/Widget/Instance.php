<?php

/**
 * Created by PhpStorm.
 * User: light
 * Date: 1/18/2019
 * Time: 11:59 PM
 */
class Potoky_ItemBanner_Model_Resource_Widget_Instance extends Mage_Widget_Model_Resource_Widget_Instance
{
    /**
     * Prepare and save layout updates data
     *
     * @param Mage_Widget_Model_Widget_Instance $widgetInstance
     * @param array $pageGroupData
     * @return array of inserted layout updates ids
     */
    protected function _saveLayoutUpdates($widgetInstance, $pageGroupData)
    {
        $writeAdapter          = $this->_getWriteAdapter();
        $pageLayoutUpdateIds   = array();
        $storeIds              = $this->_prepareStoreIds($widgetInstance->getStoreIds());
        $layoutUpdateTable     = $this->getTable('core/layout_update');
        $layoutUpdateLinkTable = $this->getTable('core/layout_link');
        $mainCondition         = Mage::registry('current_widget_instance') &&
                                 Mage::registry('current_widget_instance')->getType() == 'itembanner/banner';

        foreach ($pageGroupData['layout_handle_updates'] as $handle) {
            if ($mainCondition &&
                ($handle == 'catalogsearch_result_index' || $handle == 'catalogsearch_advanced_result')) {
                $pageGroupData['block_reference'] = 'search_result_list';
            }
            $xml = $widgetInstance->generateLayoutUpdateXml(
                $pageGroupData['block_reference'],
                $pageGroupData['template']
            );
            $insert = array(
                'handle'     => $handle,
                'xml'        => $xml
            );
            if (strlen($widgetInstance->getSortOrder())) {
                $insert['sort_order'] = $widgetInstance->getSortOrder();
            };

            $writeAdapter->insert($layoutUpdateTable, $insert);
            $layoutUpdateId = $writeAdapter->lastInsertId($layoutUpdateTable);
            $pageLayoutUpdateIds[] = $layoutUpdateId;

            $data = array();
            foreach ($storeIds as $storeId) {
                $data[] = array(
                    'store_id'         => $storeId,
                    'area'             => $widgetInstance->getArea(),
                    'package'          => $widgetInstance->getPackage(),
                    'theme'            => $widgetInstance->getTheme(),
                    'layout_update_id' => $layoutUpdateId);
            }
            $writeAdapter->insertMultiple($layoutUpdateLinkTable, $data);
        }
        return $pageLayoutUpdateIds;
    }

    public function getWriteConnection()
    {
        return $this->_getWriteAdapter();
    }
}