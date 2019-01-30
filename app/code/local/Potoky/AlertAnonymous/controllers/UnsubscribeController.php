<?php

require_once(
    Mage::getModuleDir('controllers','Mage_ProductAlert').
    DS.'UnsubscribeController.php');
class Potoky_AlertAnonymous_UnsubscribeController extends Mage_ProductAlert_UnsubscribeController
{
    public static $helpers;

    public function preDispatch()
    {
        Mage::helper('alertanonymous')->setUpHelpers($this);
        $unsubscribeInfo = $this->getRequest()->getParam('unsubscribe');
        $customerIdentifiers = explode(
            ' ',
            rawurldecode($unsubscribeInfo)
        );

        $email = $customerIdentifiers[0];
        $websiteId = $customerIdentifiers[1];;

        $customer = self::$helpers['entity']->getCustomerEntityByRequest('customer/customer', $email, $websiteId);
        if ($customerId = $customer->getId()) {
            parent::preDispatch();
            $session = Mage::getSingleton('customer/session');
            $sessionId = $session->getId();
            if (!$sessionId || $customerId == $sessionId) {
                self::$helpers['registry']->setRegistry(null, $customer, true);
            } else {
                $session->setId(null);
                $session->addNotice(
                    'There was a Customer logged in another than that You\'ve been trying to unsubscribe. Please repeat the trial.');
                $this->setFlag('', 'no-dispatch', true);
                $this->_redirect('customer/account/');
            }

            return;
        }

        Mage_Core_Controller_Front_Action::preDispatch();

        $session = Mage::getSingleton('customer/session');
        if (!$sessionId = $session->getId()) {
            $anonymousCustomer = self::$helpers['entity']->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);
            self::$helpers['registry']->setRegistry(null, $anonymousCustomer, false);
        } else {
            $session->setId(null);
            $session->addNotice(
                'There was a Customer logged in another than that You\'ve been trying to unsubscribe. Please repeat the trial.');
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('customer/account/');
        }
    }
}