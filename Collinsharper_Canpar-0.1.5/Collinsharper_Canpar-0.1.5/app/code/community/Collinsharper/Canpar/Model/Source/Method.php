<?php

class Collinsharper_Canpar_Model_Source_Method
{
    public function toOptionArray()
    {
        $model = Mage::getSingleton('canparmodule/Carrier_Shippingmethod');
        $arr = array();
        foreach ($model->getCode('method') as $v => $l)
        {
            $arr[] = array('value' => $v, 'label' => $l);
        }
        return $arr;
    }
}
