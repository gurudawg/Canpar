<?php
/**
 * Created by Gayan Hewa
 * User: Gayan
 * Date: 9/16/13
 * Time: 12:22 AM
 */

class Collinsharper_Canpar_Block_Adminhtml_Canparshipment extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'canparmodule';
        $this->_controller = 'adminhtml_canparshipment';
        //$manifest_id = $this->getRequest()->getParam('manifest_id');
        if (!empty($manifest_id)) {
            $this->_headerText = Mage::helper('sales')->__('View Shipment');
        } else {
            $this->_headerText = Mage::helper('sales')->__('Create Shipment');
        }

        parent::__construct();

        $this->_removeButton('add');
        //$this->_addBackButton();

    }


    //protected function getBackUrl() { return $this->getUrl('*/canparmanifest'); }

}