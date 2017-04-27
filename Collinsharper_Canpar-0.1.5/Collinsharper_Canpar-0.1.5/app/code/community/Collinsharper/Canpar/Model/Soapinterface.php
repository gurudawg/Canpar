<?php
/**
 * Created by Gayan Hewa
 * User: Gayan
 * Date: 7/16/13
 * Time: 10:29 PM
 */

class Collinsharper_Canpar_Model_Soapinterface
{
    //Client type
    protected $_services;

    public function __construct()
    {
        $url = Mage::helper('canparmodule')->getConfig('endpoint');
        //if sandbox
        $this->_services["rating"] = array(
            'wsdl' => $url.'?wsdl',
            'endpoint' => $url
        );

        $url = Mage::helper('canparmodule')->getConfig('business_endpoint');

        $this->_services["business"] = array(
            'wsdl' => $url.'?wsdl',
            'endpoint' => $url
        );

        $url = Mage::helper('canparmodule')->getConfig('addon_endpoint');
        $this->_services["addson"] = array(
            'wsdl' => $url."?wsdl",
            'endpoint' => $url
        );


    }

    public function getClient($type)
    {
        // HACK: SoapClient in PHP >= 5.3.0 faults if wsdl is sent using chunked-encoding (which Canpar does)... this gets around that.
        $url = $this->_services[$type]["wsdl"];
        $tmpWsdlPath = tempnam(Mage::getBaseDir('tmp'), $type . '-wsdl');
        copy($url, $tmpWsdlPath);

        $client = new SoapClient($tmpWsdlPath);
        //Store the service client in a registry just in case ;)
        //Mage::register($type,$client);
        return $client;
    }
}