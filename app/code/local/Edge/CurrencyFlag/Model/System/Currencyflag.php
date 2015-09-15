<?php
/**
 * Edge currency flag block
 *
 * @category   Edge
 * @package    Edge_CurrencyFlag
 * @author     outer/edge <hello@outeredgeuk.com>
 */
class Edge_CurrencyFlag_Model_System_Currencyflag
{
    /**
     * Custom currency flag properties
     *
     * @var array
     */
    protected $_flagsData = array();

    /**
     * Store id
     *
     * @var string | null
     */
    protected $_storeId;

    /**
     * Website id
     *
     * @var string | null
     */
    protected $_websiteId;
    /**
     * Cache types which should be invalidated
     *
     * @var array
     */
    protected $_cacheTypes = array(
        'config',
        'block_html',
        'layout'
    );

    /**
     * Config path to custom currency flag value
     */
    const XML_PATH_CUSTOM_CURRENCY_FLAG = 'currency/options/customflag';
    const XML_PATH_ALLOWED_CURRENCIES     = 'currency/options/allow';

    /*
     * Separator used in config in allowed currencies list
     */
    const ALLOWED_CURRENCIES_CONFIG_SEPARATOR = ',';

    /**
     * Config currency section
     */
    const CONFIG_SECTION = 'currency';

    /**
     * Sets store Id
     *
     * @param  $storeId
     * @return Edge_CurrencyFlag_Model_System_Currencyflag
     */
    public function setStoreId($storeId=null)
    {
        $this->_storeId = $storeId;
        $this->_flagsData = array();

        return $this;
    }

    /**
     * Sets website Id
     *
     * @param  $websiteId
     * @return Edge_CurrencyFlag_Model_System_Currencyflag
     */
    public function setWebsiteId($websiteId=null)
    {
        $this->_websiteId = $websiteId;
        $this->_flagsData = array();

        return $this;
    }

    /**
     * Returns currency flag properties array based on config values
     *
     * @return array
     */
    public function getCurrencyFlagsData()
    {
        if ($this->_flagsData) {
            return $this->_flagsData;
        }

        $this->_flagsData = array();

        $allowedCurrencies = explode(
            self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
            Mage::getStoreConfig(self::XML_PATH_ALLOWED_CURRENCIES, null)
        );

        /* @var $storeModel Mage_Adminhtml_Model_System_Store */
        $storeModel = Mage::getSingleton('adminhtml/system_store');
        foreach ($storeModel->getWebsiteCollection() as $website) {
            $websiteShow = false;
            foreach ($storeModel->getGroupCollection() as $group) {
                if ($group->getWebsiteId() != $website->getId()) {
                    continue;
                }
                foreach ($storeModel->getStoreCollection() as $store) {
                    if ($store->getGroupId() != $group->getId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $websiteShow = true;
                        $websiteFlags  = $website->getConfig(self::XML_PATH_ALLOWED_CURRENCIES);
                        $allowedCurrencies = array_merge($allowedCurrencies, explode(
                            self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                            $websiteFlags
                        ));
                    }
                    $storeFlags = Mage::getStoreConfig(self::XML_PATH_ALLOWED_CURRENCIES, $store);
                    $allowedCurrencies = array_merge($allowedCurrencies, explode(
                        self::ALLOWED_CURRENCIES_CONFIG_SEPARATOR,
                        $storeFlags
                    ));
                }
            }
        }
        ksort($allowedCurrencies);

        $currentFlags = $this->_unserializeStoreConfig(self::XML_PATH_CUSTOM_CURRENCY_FLAG);

        /** @var $locale Mage_Core_Model_Locale */
        foreach ($allowedCurrencies as $code) {
            if (!$name) {
                $name = $code;
            }
            $this->_flagsData[$code] = array(
                'parentFlag'  => $flag,
                'displayName' => $name
            );

            if (isset($currentFlags[$code]) && !empty($currentFlags[$code])) {
                $this->_flagsData[$code]['displayFlag'] = $currentFlags[$code];
            } else {
                $this->_flagsData[$code]['displayFlag'] = $this->_flagsData[$code]['parentFlag'];
            }
            if ($this->_flagsData[$code]['parentFlag'] == $this->_flagsData[$code]['displayFlag']) {
                $this->_flagsData[$code]['inherited'] = true;
            } else {
                $this->_flagsData[$code]['inherited'] = false;
            }
        }

        return $this->_flagsData;
    }

    /**
     * Saves currency flag to config
     *
     * @param  $flags array
     * @return Mage_CurrencyFlag_Model_System_Currencyflag
     */
    public function setCurrencyFlagsData($flags=array())
    {   
        if ($flags) {
            $value['options']['fields']['customflag']['value'] = serialize($flags);
        }

        Mage::getModel('adminhtml/config_data')
            ->setSection(self::CONFIG_SECTION)
            ->setWebsite(null)
            ->setStore(null)
            ->setGroups($value)
            ->save();

        // reinit configuration
        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

        $this->clearCache();

        Mage::dispatchEvent('admin_system_config_changed_section_currency',
            array('website' => $this->_websiteId, 'store' => $this->_storeId)
        );

        return $this;
    }

    /**
     * Returns custom currency flag by currency code
     *
     * @param  $code
     * @return bool|string
     */
    public function getCurrencyFlag($code)
    {
        $customFlags = $this->_unserializeStoreConfig(self::XML_PATH_CUSTOM_CURRENCY_FLAG);
        if (array_key_exists($code, $customFlags)) {
            return $customFlags[$code];
        }

        return false;
    }

    /**
     * Clear translate cache
     *
     * @return Saas_Translate_Helper_Data
     */
    public function clearCache()
    {
        // clear cache for frontend
        foreach ($this->_cacheTypes as $cacheType) {
            Mage::app()->getCacheInstance()->invalidateType($cacheType);
        }
        return $this;
    }

    /**
     * Unserialize data from Store Config.
     *
     * @param string $configPath
     * @param int $storeId
     * @return array
     */
    protected function _unserializeStoreConfig($configPath, $storeId = null)
    {
        $result = array();
        $configData = (string)Mage::getStoreConfig($configPath, $storeId);
        if ($configData) {
            $result = unserialize($configData);
        }

        return is_array($result) ? $result : array();
    }
}
