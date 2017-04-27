<?php

class Collinsharper_Canpar_Block_Adminhtml_Canparmanifest_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('collinsharper_canpar_canparmanifest_grid');
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('canparmodule/manifest')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare and add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('manifest_id', array(
            'header'    => Mage::helper('sales')->__('Manifest #'),
            'index'     => 'manifest_id',
            'filter_index'     => 'main_table.manifest_id',
            'type'      => 'text',
        ));

        $this->addColumn('canpar_manifest_num', array(
            'header'    => Mage::helper('sales')->__('Canpar Manifest #'),
            'index'     => 'canpar_manifest_num',
            'filter_index'     => 'main_table.canpar_manifest_num',
            'type'      => 'text',
        ));

        $this->addColumn('created_date', array(
            'header'    => Mage::helper('sales')->__('Date Manifested'),
            'index'     => 'created_date',
            'filter_index' =>'main_table.created_date',
            'type'      => 'datetime',
        ));

//        $this->addColumn('action',
//            array(
//                'header'    => Mage::helper('sales')->__('Action'),
//                'width'     => '50px',
//                'type'      => 'action',
//                'getter'     => 'getManifestId',
//                'actions'   => array(
//                    array(
//                        'caption' => Mage::helper('sales')->__('View'),
//                        'url'     => array('base'=>'*/*/view'),
//                        'field'   => 'manifest_id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'is_system' => true
//            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/shipment')) {
            return false;
        }

        return $this->getUrl('*/*/massPrint',
            array(
                'manifest_ids'=> $row->getManifestId()  ,
            )
        );
    }

    /**
     * Prepare and set options for massaction
     *
     * @return Mage_Adminhtml_Block_Sales_Shipment_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('manifest_id');
        $this->getMassactionBlock()->setFormFieldName('manifest_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('get_label', array(
            'label'=> Mage::helper('sales')->__('Print Manifests'),
            'url'  => $this->getUrl('*/*/massPrint'),
        ));
//
//        $this->getMassactionBlock()->addItem('print_shipping_label', array(
//            'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
//            'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
//        ));

        return $this;
    }

    /**
     * Get url of grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true));
    }

}
