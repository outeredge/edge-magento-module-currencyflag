<?php

/**
 * Edge currency flag block
 *
 * @category   Edge
 * @package    Edge_CurrencyFlag
 * @author     outer/edge <hello@outeredgeuk.com>
 */
class Edge_CurrencyFlag_Block_Adminhtml_System_Currencyflag extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Constructor. Initialization required variables for class instance.
     */
    public function __construct()
    {
        $this->_blockGroup = 'currencyflag_system';
        $this->_controller = 'adminhtml_system_currencyflag';
        parent::__construct();
    }

    /**
     * Custom currency flag properties
     *
     * @var array
     */
    protected $_flagsData = array();

    /**
     * Prepares layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Returns page header
     *
     * @return bool|string
     */
    public function getHeader()
    {
        return Mage::helper('adminhtml')->__('Manage Currency Flags');
    }

    /**
     * Returns 'Save Currency Flag' button's HTML code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button');
        $block->setData(array(
            'label'     => Mage::helper('currencyflag')->__('Save Currency Flags'),
            'onclick'   => 'currencyFlagsForm.submit();',
            'class'     => 'save'
        ));

        return $block->toHtml();
    }

    /**
     * Returns URL for save action
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Returns website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getRequest()->getParam('website');
    }

    /**
     * Returns store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }

    /**
     * Returns Custom currency flag properties
     *
     * @return array
     */
    public function getCurrencyFlagsData()
    {
        if(!$this->_flagsData) {
            $this->_flagsData =  Mage::getModel('currencyflag/system_currencyflag')
                ->getCurrencyFlagsData();
        }
        return $this->_flagsData;
    }
}
