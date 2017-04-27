<?php


class Collinsharper_Canpar_Model_Order_Observer
{
    public function save_canpar_options($observer) {

        $order = $observer->getEvent()->getOrder();
        $canparOptions = $this->getStoredCanparOptions();

        if($canparOptions) {

            $order->setCanparShippingOptions(serialize($canparOptions));

            $order->save();

        }

         return $this;

    }

    function getStoredCanparOptions() {

        return unserialize($this->_getSession()->getData('canpar_options'));

    }

    function _getSession() {

        if (Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('admin/session');
        }

        return mage::getSingleton('core/session');

    }

}