<?php

class Collinsharper_Canpar_Model_Source_Handling extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * To options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $arr = array();
        $arr[] = array('value' => "%", 'label' => '% from total');
        $arr[] = array('value' => "fixed", 'label' => 'Fixed');
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

