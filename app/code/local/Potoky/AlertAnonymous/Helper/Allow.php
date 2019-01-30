<?php

class Potoky_AlertAnonymous_Helper_Allow extends Mage_Core_Helper_Abstract
{
    /**
     * Check whether this type of alert is available to subscribe to
     * for anonymous customer too
     *
     * @param null $templateId
     * @return mixed
     */
    public function isCurrentAlertAllowedForAnonymous($templateId = null)
    {
        if (Mage::getDesign()->getPackageName() != 'potoky' ||
            Mage::getDesign()->getTheme('template') != 'alertanonymous' ||
            Mage::getDesign()->getTheme('skin') != 'alertanonymous' ||
            Mage::getDesign()->getTheme('layout') != 'alertanonymous') {
            return false;
        }
        if($templateId === null) {
            $urlParts = explode('/', Mage::helper('core/url')->getCurrentUrl());
            $alertKey = array_search('add', $urlParts) + 1;
            $templateId = $urlParts[$alertKey];
        }

        if (!Mage::getStoreConfig(sprintf('catalog/productalert/allow_%s', $templateId)) ||
            !Mage::getStoreConfig(sprintf('catalog/productalert/allow_%s_anonymous', $templateId))) {
            return false;
        }

        return true;
    }
}