<?php
/**
 * Created by Gayan Hewa
 * User: Gayan
 * Date: 9/15/13
 * Time: 8:45 PM
 */

class Collinsharper_Canpar_Block_Adminhtml_Canparmanifest extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {

        $this->_blockGroup = 'canparmodule';
        $this->_controller = 'adminhtml_canparmanifest';
        $this->_headerText = Mage::helper('sales')->__('Canpar Manifests');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('add', array(
            'label'     => Mage::helper('canparmodule')->__('Run End Of Day'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/create').'\');',
            'class'     => 'add',
        ));
    }
}