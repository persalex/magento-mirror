<?php

class Potoky_ItemBanner_AjaxController extends Mage_Core_Controller_Front_Action
{
    /**
     * Increase click quantity by one right after GoTo button on the banner's popup has been clicked on
     *
     * @return void
     */
    public function clickincrementAction()
    {
        $instanceId = $this->getRequest()->getParam('instanceId');

        if (!$instanceId) {

            return;
        }

        /* @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = Mage::getModel('widget/widget_instance')->load($instanceId);

        if (!$widgetInstance) {

            return;
        }

        $resource = $widgetInstance->getResource();
        $writeAdapter = $resource->getWriteConnection();
        $parameters = $widgetInstance->getWidgetParameters();
        $parameters['goto']++;
        $data = ['widget_parameters' => serialize($parameters)];
        $writeAdapter->update($resource->getMainTable(), $data, array('instance_id = ?' => (int)$instanceId));
    }
}