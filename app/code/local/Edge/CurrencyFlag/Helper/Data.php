<?php

/**
 * Edge currency flag block
 *
 * @category   Edge
 * @package    Edge_CurrencyFlag
 * @author     outer/edge <hello@outeredgeuk.com>
 */
class Edge_CurrencyFlag_Helper_Data extends Mage_Core_Helper_Data
{

    /**
     * Get currency display options
     *
     * @param string $baseCode
     * @return array
     */
    public function getCurrencyOptions($baseCode)
    {
        $currencyOptions = array();
        $currencyFlag = Mage::getModel('currencyflag/system_currencyflag');
        if($currencyFlag) {
            $customCurrencyFlag = $currencyFlag->getCurrencyFlag($baseCode);

            if ($customCurrencyFlag) {
                $currencyOptions['flag']  = $customCurrencyFlag;
                $currencyOptions['display'] = Zend_Currency::USE_FLAG;
            }
        }

        return $currencyOptions;
    }
}
