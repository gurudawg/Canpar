<?php

class Collinsharper_Canpar_Model_Carrier_Shippingmethod extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_type;
    protected $_constraints;
    protected $_code = 'canparmodule';
    protected $_result = null;

    public function __construct()
    {
        $this->_type = array(
            "1" => "GROUND",
            "2" => "U.S.A",
            "3" => "SELECT LETTER",
            "4" => "SELECT PAK",
            "5" => "SELECT",
            "C" => "EXPRESS LETTER",
            "D" => "EXPRESS PAK",
            "E" => "EXPRESS",
            "F" => "U.S.A LETTER",
            "G" => "U.S.A PAK",
            "H" => "SELECT U.S.A",
            "I" => "INTERNATIONAL"
        );

        // This is the limit of the sum of all package
        $this->_constraints = array(
            "1"=>array(
                "min"=>1,
                "max"=>1000
            ),
            "2"=>array(
                "min"=>1,
                "max"=>1000
            ),
            "3"=>array(
                "min"=>0,
                "max"=>1
            ),
            "4"=>array(
                "min"=>1,
                "max"=>5
            ),
            "5"=>array(
                "min"=>1,
                "max"=>75
            ),
            "C"=>array(
                "min"=>0,
                "max"=>1
            ),
            "D"=>array(
                "min"=>1,
                "max"=>3
            ),
            "E"=>array(
                "min"=>1,
                "max"=>75
            ),
            "F"=>array(
                "min"=>1,
                "max"=>999999
            ),
            "G"=>array(
                "min"=>1,
                "max"=>3
            ),
            "H"=>array(
                "min"=>1,
                "max"=>70
            ),
            "I"=>array(
                "min"=>1,
                "max"=>999999
            ),
        );

    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

        //No multishipping support
        if (Mage::app()->getRequest()->getControllerName() == "multishipping") {
            return false;
        }

        //Get other shippin methos available
        $result = Mage::getModel('shipping/rate_result');

        $rate = Mage::getModel('canparmodule/rate');
        $response = $rate->createRateRequest($request);

        if (!$response) {

            if (Mage::helper('canparmodule')->getConfig('failover_rate') > 0) {

                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier($this->_code);
                $method->setCarrierTitle(Mage::helper('canparmodule')->getConfig('title'));
                $method->setMethod('Regular');
                $method->setMethodTitle(Mage::helper('canparmodule')->getConfig('failover_ratetitle'));
                $method->setCost(Mage::helper('canparmodule')->getConfig('failover_rate'));
                $method->setPrice(Mage::helper('canparmodule')->getConfig('failover_rate'));
                $method->setBadAddress("No responses");
                $result->append($method);

            } else {

                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $result->append($error);

            }

            return $result;
        }

        $allowed_methods = $this->getAllowedMethods();
        $availableResult = $response->return->getAvailableServicesResult;

        if(isset($response->return->getAvailableServicesResult->type)) {

            $availableResult = array();
            $availableResult[] = $response->return->getAvailableServicesResult;

        }


        $hasAvailableMethods = false;
        foreach($availableResult as $type) {

            if(!isset($allowed_methods[$type->type])) {

               continue;

            }

            $quote = Mage::helper('canparmodule')->getQuote();
            $rate_request["shipment"]["packages"] = array();
        
            $weight = Mage::helper('canparmodule')->getPackageWeightLb($quote);
            
            //Minimum weight will always be 1lb if it falls below.

            if ($weight < 1) {

                $weight = 1;

            }
            
            if ($weight < $this->_constraints[$type->type]["min"] || $weight > $this->_constraints[$type->type]["max"]) {
                
                continue;
            }

            $rate_object = $rate->getRate($request, $type, $weight);

            if (!$rate_object) {

                continue;

            }
            
            $price = $rate_object->return->processShipmentResult->shipment->freight_charge +
                $rate_object->return->processShipmentResult->shipment->fuel_surcharge;

            $price += $rate_object->return->processShipmentResult->shipment->collect_charge;
            $price += $rate_object->return->processShipmentResult->shipment->tax_charge_1;
            $price += $rate_object->return->processShipmentResult->shipment->tax_charge_2;
            $price += $rate_object->return->processShipmentResult->shipment->dg_charge;
            $price += $rate_object->return->processShipmentResult->shipment->ea_charge;
            $price += $rate_object->return->processShipmentResult->shipment->xc_charge;
            $price += $rate_object->return->processShipmentResult->shipment->ra_charge;

            $cost = $price;

            if (Mage::helper('canparmodule')->getConfig('handling_type') == "fixed" && Mage::helper('canparmodule')->getConfig('handling')) {

                $price +=  Mage::helper('canparmodule')->getConfig('handling');

            } else if (Mage::helper('canparmodule')->getConfig('handling')) {

                $price +=  (Mage::helper('canparmodule')->getConfig('handling') * $price);

            }



            if ($request->getFreeShipping() == true
                || $request->getPackageQty() == $this->getFreeBoxes() ) {
//                || ($this->getConfigData('free_shipping_enable')
//                    && in_array($rate['code'], $free_methods)
//                    && (float) $subtotal >= (float) $this->getConfigData('free_shipping_subtotal'))
//            ) {
                $price = 0.00;
            }

            $date = (string)$rate_object->return->processShipmentResult->shipment->estimated_delivery_date;
            $delivery_title = "%s";

            if($date) {

                $date = date('Y-m-d', strtotime($date));

                if(Mage::helper('canparmodule')->getConfig('lead_time_days')) {

                    $days = (int)Mage::helper('canparmodule')->getConfig('lead_time_days');
                    $modified_delivery_date = strtotime("+{$days} days", strtotime($date));

                    // TODO: this does not account for Monday Holidays.

                    if(date('N', $modified_delivery_date) == 7) {
                        $modified_delivery_date = strtotime("+1 days", strtotime($date));
                    }

                    $date = date('Y-m-d', $modified_delivery_date);


                }

                $delivery_title = "%s Estimated Delivery Date %s";

            }

            $rtitle = isset($this->_type[$rate_object->return->processShipmentResult->shipment->service_type]) ? $this->_type[$rate_object->return->processShipmentResult->shipment->service_type] : 'Standard';

            $method_tite = Mage::helper('canparmodule')->__($delivery_title, $rtitle, $date);

            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setCarrierTitle(Mage::helper('canparmodule')->getConfig('title'));
//            $method->setMethod($this->_type["{$rate_object->return->processShipmentResult->shipment->service_type}"]);
            $method->setMethod($type->type);
            $method->setCost($cost);
            $method->setPrice($price);
            $method->setMethodTitle($method_tite);
            $result->append($method);

            $hasAvailableMethods = true;
        }

        if (!$hasAvailableMethods) {
            if (Mage::helper('canparmodule')->getConfig('failover_rate') > 0) {
                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier($this->_code);
                $method->setCarrierTitle(Mage::helper('canparmodule')->getConfig('title'));
                $method->setMethod('Regular');
                $method->setMethodTitle(Mage::helper('canparmodule')->getConfig('failover_ratetitle'));
                $method->setCost(Mage::helper('canparmodule')->getConfig('failover_rate'));
                $method->setPrice(Mage::helper('canparmodule')->getConfig('failover_rate'));
                $method->setBadAddress("No responses");
                $result->append($method);
            } else {
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $result->append($error);
            }
        }

        return $result;
    }


    public function getAllowedMethods()
    {

        $allowed = explode(',', $this->getConfigData('allowed_methods'));

        $arr = array();

        foreach ($allowed as $k) {

            $arr[$k] = $this->getCode('method', $k);

        }

        return $arr;

    }

    public function getCode($type, $code = '')
    {
        $codes = array(

            'method' => $this->_type,

        );

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }


    public function getTrackingInfo($tracking)
    {
        $info = array();

        $result = $this->getTracking($tracking);

        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    protected function _getCgiTracking($trackings)
    {
        //ups no longer support tracking for data streaming version
        //so we can only reply the popup window to ups.
        $result = Mage::getModel('shipping/tracking_result');
        $defaults = $this->getDefaults();
        foreach($trackings as $tracking){
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier('ups');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($tracking);
            $status->setPopup(1);

            // TODO: handle french and english? ?locale=fr
            $status->setUrl("http://www.canpar.com/en/track/TrackingAction.do?locale=en&type=0&reference=".$tracking);
            $result->append($status);
        }

        $this->_result = $result;
        return $result;
    }

    public function getTracking($trackings)
    {
        $return = array();

        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }
            $this->_getCgiTracking($trackings);

        return $this->_result;
    }



}
