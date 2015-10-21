<?php

class Edge_CurrencyFlag_Model_Observer
{
    public function saveCurrencyFlags(Varien_Event_Observer $observer)
    {
        $currencyFlags = unserialize(Mage::getStoreConfig('currency/options/currencyflag', ""));

        $deleteFlags = Mage::app()->getRequest()->getParam('delete_currency_flag', null);
        if (!empty($deleteFlags)) {
            foreach ($deleteFlags as $currency => $delete) {
                unset($_FILES[$currency], $currencyFlags[$currency]);
            }
        }

        $fileUpload = false;
        foreach ($_FILES as $image) {
            if (isset($image['name']) && $image['name'] !== '') {
                $fileUpload = true;
            }
        }
        if ($fileUpload) {
            foreach ($_FILES as $currency => $image) {
                if (isset($image['name']) && $image['name'] != '') {
                    try {
                        $uploader = new Mage_Core_Model_File_Uploader($currency);
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','svg'));
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(false);

                        $dirPath = Mage::getBaseDir('media') . DS . 'currencyflag' . DS;
                        $result = $uploader->save($dirPath, $image['name']);
                        $currencyFlags[$currency] = 'currencyflag' . DS . $result['file'];

                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    }
                }
            }
        }

        if (!empty($currencyFlags)) {
            $config = array('value' => serialize($currencyFlags));
        } else {
            $config = array('inherit' => 1);
        }

        Mage::getModel('adminhtml/config_data')
            ->setSection('currency')
            ->setWebsite(null)
            ->setStore(null)
            ->setGroups(array(
                'options' => array(
                    'fields' => array(
                        'currencyflag' => $config
                    )
                )))
            ->save();

        Mage::getSingleton('connect/session')->addSuccess(
            Mage::helper('adminhtml')->__('Custom currency flags were applied successfully.')
        );
    }
}