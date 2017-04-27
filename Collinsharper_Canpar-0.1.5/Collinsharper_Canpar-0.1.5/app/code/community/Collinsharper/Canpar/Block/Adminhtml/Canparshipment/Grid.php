<?php
/**
 * Created by Gayan Hewa
 * User: Gayan
 * Date: 9/16/13
 * Time: 12:23 AM
 */

class Collinsharper_Canpar_Block_Adminhtml_Canparshipment_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('canparshipment_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_shipment';
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('sales/order_shipment')->getCollection();
        $tableName = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');

            $collection->getSelect()->joinLeft(array('o'=>$tableName), 'main_table.order_id=o.entity_id', array(
            'order_increment_id'=>'o.increment_id',
            'order_created_date'=>'o.created_at',
            'o.shipping_description'));

        $collection->addFieldToFilter('o.shipping_description', array('like'=>'%canpar%'));
        $tableName = Mage::getSingleton('core/resource')->getTableName('ch_canpar_shipment');

        $collection->getSelect()->joinLeft(array('ch_shipment'=>$tableName), 'main_table.entity_id=ch_shipment.magento_shipment_id',array(
            'canpar_shipment_id'=>'shipment_id',
            'manifest_id'=>'manifest_id'));

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
        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'increment_id',
            'filter_index'     => 'main_table.increment_id',
            'type'      => 'text',
        ));
       $this->addColumn('shipment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'entity_id',
            'filter_index'     => 'main_table.entity_id',
            'type'      => 'text',
        ));

        $this->addColumn('canpar_shipment_id', array(
            'header'    => Mage::helper('sales')->__('Canpar Shipment #'),
            'index'     => 'canpar_shipment_id',
            'filter_index'     => 'ch_shipment.shipment_id',
            'type'      => 'text',
        ));

        $this->addColumn('manifest_id', array(
            'header'    => Mage::helper('sales')->__('Canpar Manifest #'),
            'index'     => 'manifest_id',
            'renderer'  => 'canparmodule/adminhtml_canparshipment_renderer_manifestId',
            'filter_index'     => 'ch_shipment.manifest_id',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Date Shipped'),
            'index'     => 'created_at',
            'filter_index' =>'main_table.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'filter_index'=> 'o.increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_date', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_date',
            'filter_index' =>'o.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_shipment/view'),
                        'field'   => 'shipment_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            ));

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

        return $this->getUrl('*/sales_shipment/view',
            array(
                'shipment_id'=> $row->getEntityId(),
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
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('create_shipment', array(
            'label'=> Mage::helper('sales')->__('Create Canpar Shipments'),
            'url'  => $this->getUrl('*/canparshipment/massCreate'),
        ));

        $this->getMassactionBlock()->addItem('void_shipment', array(
            'label'=> Mage::helper('sales')->__('Void Canpar Shipments'),
            'url'  => $this->getUrl('*/canparshipment/massVoid'),
        ));

        $this->getMassactionBlock()->addItem('get_label', array(
            'label'=> Mage::helper('sales')->__('Print Labels'),
            'url'  => $this->getUrl('*/canparshipment/massLabels'),
        ));

        $this->getMassactionBlock()->addItem('get_tlabel', array(
            'label'=> Mage::helper('sales')->__('Print Thermal Labels'),
            'url'  => $this->getUrl('*/canparshipment/massLabels', array('_thermal' => true)),
        ));

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


