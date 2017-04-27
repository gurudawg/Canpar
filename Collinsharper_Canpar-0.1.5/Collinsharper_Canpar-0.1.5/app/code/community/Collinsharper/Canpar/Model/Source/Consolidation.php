<?php

class Collinsharper_Canpar_Model_Source_Consolidation extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * To options array
     *
     * @return array
     */
    public function toOptionArray()
    {

        $arr = array();
        $arr[] = array('value' => 0, 'label' => 'No Consolidation');
        $arr[] = array('value' => 1, 'label' => 'Adress Line 1 + Postal Code');
        $arr[] = array('value' => 2, 'label' => 'Adress Line 2 + Postal Code');
        $arr[] = array('value' => 3, 'label' => 'Name + Adress Line 1 + Postal Code');
        $arr[] = array('value' => 4, 'label' => 'Consignee Number + Postal Code');
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

