<?php
/**
 * Edge currency flag block
 *
 * @category   Edge
 * @package    Edge_CurrencyFlag
 * @author     outer/edge <hello@outeredgeuk.com>
 */
class Edge_CurrencyFlag_Model_Observer
{
    /**
     * Generate options for currency displaying with custom currency flag
     *
     * @param Varien_Event_Observer $observer
     * @return Edge_CurrencyFlag_Model__Observer
     */
    public function currencyDisplayOptions(Varien_Event_Observer $observer)
    {
        $baseCode = $observer->getEvent()->getBaseCode();
        $currencyOptions = $observer->getEvent()->getCurrencyOptions();
        $currencyOptions->setData(Mage::helper('currencyflag')->getCurrencyOptions($baseCode));

        return $this;
    }
}
