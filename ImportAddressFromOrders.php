<?php

require_once('app/Mage.php'); //Path to Magento
umask(0);
Mage::app();


/*
 * 1- get all customers
 * 2- check if customer has billing address
 * 3 - get last order from customer
 * 4 - add address to billing address
 */


$customerCollection = Mage::getModel('customer/customer')->getCollection();
echo $customerCollection->count();

foreach ($customerCollection as $customer) {

    if ($customer->getAddressesCollection()->count() == 0) {
        print('customerID:' . $customer->getId());
        $lastOrderId = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addAttributeToSort('created_at', 'DESC')
            ->getFirstItem()
            ->getId();

        if ($lastOrderId) {
            echo ' - lastOrderID:' . $lastOrderId;

            $_order = Mage::getModel('sales/order')->load($lastOrderId);
            if (is_null($_order)){
                continue;
            }


            $orderBillingAddress = $_order->getBillingAddress();

            $newAddress = Mage::getModel('customer/address');
            $newAddress->setCustomerId($customer->getId())
                ->setFirstname($orderBillingAddress->getFirstname())
                ->setMiddleName($orderBillingAddress->getFirstname())
                ->setLastname($orderBillingAddress->getLastname())
                ->setCountryId($orderBillingAddress->getCountryId())
                ->setRegionId($orderBillingAddress->getRegionId())
                ->setPostcode($orderBillingAddress->getPostcode())
                ->setCity($orderBillingAddress->getCity())
                ->setTelephone($orderBillingAddress->getTelephone())
                ->setStreet($orderBillingAddress->getStreet())
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');

                try {
                    $newAddress->save();
                    $addressId = $newAddress->getId();
                    if ($addressId ){
                        echo ' - idAddress saved: ' . $addressId;
                        echo ' - ' ;
                        printf(json_encode($newAddress->getData()));
                        $newAddress->clearInstance();
                    }
                } catch (Exception $ex) {
                    Zend_Debug::dump($ex->getMessage());
                }


        } else {
            echo " - never ordered, can't fill address";
        }
        echo PHP_EOL;

    }


}
