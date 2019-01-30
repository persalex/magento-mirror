<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'AddController.php');
class Potoky_AlertAnonymous_AddController extends Mage_ProductAlert_AddController
{
    const REFRESHED_PAGE_MESSAGE = 'Action failed. The page has been reloaded as some of its links were expired.';
    public static $helpers;

    public function preDispatch()
    {
        Mage::helper('alertanonymous')->setUpHelpers($this);

        $email = $this->getRequest()->getParam('email');

        if(!self::$helpers['allow']->isCurrentAlertAllowedForAnonymous()) {
            if ($email != null) {
                Mage_Core_Controller_Front_Action::preDispatch();
                Mage::getSingleton('catalog/session')
                    ->addError(self::REFRESHED_PAGE_MESSAGE);
                $this->setFlag('', 'no-dispatch', true);
                $this->_redirectReferer();
            } else {
                parent::preDispatch();
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                self::$helpers['registry']->setRegistry('add', $customer, true);
            }

            return;
        }
        Mage_Core_Controller_Front_Action::preDispatch();

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            if ($email != null) {
                Mage::getSingleton('catalog/session')
                    ->addError(self::REFRESHED_PAGE_MESSAGE);
                $this->setFlag('', 'no-dispatch', true);
                $this->_redirectReferer();
            } else {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                self::$helpers['registry']->setRegistry('add', $customer, true);
            }

            return;
        }

        $websiteId = Mage::app()->getWebsite()->getId();

        if(!$email || !$websiteId) {
            Mage::getSingleton('catalog/session')
                ->addError(self::REFRESHED_PAGE_MESSAGE);

            return;
        }

        $customer = self::$helpers['entity']->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($customer->getId()) {
            self::$helpers['registry']->setRegistry('add', $customer, true);
            return;
        }

        $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
        if ($anonymousCustomer->getId()) {
            self::$helpers['registry']->setRegistry('add', $anonymousCustomer, false);
            return;
        }

        $anonymousCustomer->setWebsiteId($websiteId)
            ->setEmail($email)
            ->setGroupId(0)
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setCheckIfRegistered(false);
        try {
            $anonymousCustomer->save();
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }

        self::$helpers['registry']->setRegistry('add', $anonymousCustomer, false);
    }
}
