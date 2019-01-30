<?php

class Potoky_AnonymousCustomer_Model_Observer
{
    /**
     * Initiate anonymous customer creation/deletion when corresponding
     * regular customer is created
     *
     * @var Varien_Event_Observer $observer
     */
    public function regularCustomerCreated(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $anonymousCustomer = $this->findCorrespondingAnonymous($customer);
        if($id = $anonymousCustomer->getId()) {
            if (Mage::getStoreConfig('anonymous/cascade_delete/when') == 2) {
                $this->doToAnonymous($anonymousCustomer, 'delete');
                return;
            }
            $anonymousCustomer
                ->setRegistrationId($customer->getId())
                ->setRegisteredAt($customer->getCreatedAt());
            $this->doToAnonymous($anonymousCustomer, 'save');
        }
    }

    /**
     * Initiate anonymous customer creation/deletion when corresponding
     * regular customer is deleted
     *
     * @var Varien_Event_Observer $observer
     */
    public function regularCustomerDeleted(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $anonymousCustomer = $this->findCorrespondingAnonymous($customer);
        if($id = $anonymousCustomer->getId()) {
            if (Mage::getStoreConfig('anonymous/cascade_delete/when') == 1) {
                $this->doToAnonymous($anonymousCustomer, 'delete');
                return;
            }
            $anonymousCustomer
                ->unsetRegistrationId()
                ->unsetRegisteredAt();
            $this->doToAnonymous($anonymousCustomer, 'save');
        }
    }

    /**
     * Find anonymous customer corresponding to regular customer
     *
     * @var $customer
     */
    private function findCorrespondingAnonymous($customer)
    {
        $email = $customer->getEmail();
        $websiteId = $customer->getWebsiteId();
        $anonymousCustomer = Mage::helper('anonymouscustomer/entity')
            ->getCustomerEntityByRequest('anonymouscustomer/anonymous', $email, $websiteId);

        return $anonymousCustomer;
    }

    /**
     * Process anonymous customer save/delete depending on events
     * happening on regular customer
     *
     * @var $anonymousCustomer
     * @var $action
     */
    private function doToAnonymous($anonymousCustomer, $action)
    {
        try {
            $anonymousCustomer->$action();
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
        }
    }
}