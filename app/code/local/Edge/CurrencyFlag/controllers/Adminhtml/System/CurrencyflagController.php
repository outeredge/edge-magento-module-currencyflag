<?php
/**
 * Edge currency flag block
 *
 * @category   Edge
 * @package    Edge_CurrencyFlag
 * @author     outer/edge <hello@outeredgeuk.com>
 */
class Edge_CurrencyFlag_Adminhtml_System_CurrencyflagController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Show Currency Glag's Management dialog
     */
    public function indexAction()
    {
        // set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('system/currency')
            ->_addBreadcrumb(
                Mage::helper('currencyflag')->__('System'),
                Mage::helper('currencyflag')->__('System')
            )
            ->_addBreadcrumb(
                Mage::helper('currencyflag')->__('Manage Currency Rates'),
                Mage::helper('currencyflag')->__('Manage Currency Rates')
            );

        $this->_title($this->__('System'))
            ->_title($this->__('Manage Currency Rates'));
        $this->renderLayout();
    }

    /**
     * Save custom Currency flag
     */
    public function saveAction()
    {   
        $flagsDataArray = array();
        
        if (!empty($_FILES)) {
            foreach ($_FILES as $name => $fileData) {
                if (isset($fileData['name']) && $fileData['name'] != '') {
                    $flagsDataArray[$name] = $fileData['name'];
                    try {
                        
                        $uploader = new Mage_Core_Model_File_Uploader($name);
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','svg'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);

                        $dirPath = Mage::getBaseDir('media') . DS . 'currencyflag';
                        
                        $uploader->save($dirPath, $_FILES[$name]['name']);
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }
        }

        if (is_array($flagsDataArray)) {
            foreach ($flagsDataArray['custom_currency_flag'] as &$flagsData) {
                $flagsData = Mage::helper('adminhtml')->stripTags($flagsData);
            }
        }

        try {
            Mage::getModel('currencyflag/system_currencyflag')->setCurrencyFlagsData($flagsDataArray);
            Mage::getSingleton('connect/session')->addSuccess(
                Mage::helper('currencyflag')->__('Custom currency symbols were applied successfully.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }        

        $this->_redirectReferer();
    }

    /**
     * Resets custom Currency flag for all store views, websites and default value
     */
    public function resetAction()
    {
        Mage::getModel('currencyflag/system_currencyflag')->resetValues();
        $this->_redirectReferer();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/currency/flags');
    }
}
