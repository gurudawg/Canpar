<?php

class Collinsharper_Canpar_Model_Source_Ccy extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * To options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $arr = array();
        $arr[] = array('value' => "USD", 'label' => 'USD');
        $arr[] = array('value' => "CAD", 'label' => 'CAD');
        return $arr;
    }

    /**
     * Get all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * To options hash
     *
     * @return String
     */
    public function toOptionHash()
    {
        $source = $this->_getSource();
        return $source ? $source->toOptionHash() : array();
    }

}

