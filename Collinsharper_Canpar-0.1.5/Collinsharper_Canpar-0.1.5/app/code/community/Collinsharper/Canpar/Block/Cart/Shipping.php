<?php

class Collinsharper_Canpar_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    public function getCityActive() {
        return (bool)Mage::getStoreConfig('carriers/dhl/active')
            || (bool)Mage::getStoreConfig('carriers/dhlint/active')
            || (bool)Mage::getStoreConfig('carriers/canparmodule/active');
    }

    public function getEstimateRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
        $return_rates = array();

        return $this->_rates;
    }


}