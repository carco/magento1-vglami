<?php

/**
 * Glami PiXel 
 * 
 * @author Emil Sirbu <emil.sirbu@gmail.com>
 * 
 * Based on Cadence Facebook Pixel
 * @author Alan Barber <alan@cadence-labs.com>
 * 
 */
Class Vasilica_Glami_Model_Observer
{
    /**
     * @param Varien_Event_Observer $obs
     * @return $this
     */
    public function onSalesQuoteProductAddAfter(Varien_Event_Observer $obs)
    {
        if (!$this->helper()->isAddToCartEnabled()) {
            return $this;
        }
        
        /*
            Mage::dispatchEvent('sales_quote_product_add_after', array('items' => $items));
        */
        
        /** @var array $items */
        $items = $obs->getItems();

        $data = array_replace(array(
            'item_ids' => [],
            'product_names' => [], 
            'value' => 0.00
        ), $this->getSession()->getAddToCart() ?: array());

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $data['item_ids'][] = $item->getSku();
            $data['product_names'][] = $item->getName();
            $data['value'] += $item->getProduct()->getFinalPrice() * $item->getProduct()->getQty();
        }

        $data['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        if ($data['item_ids']) {
            $this->getSession()->setAddToCart($data);
        } else {
            $this->getSession()->unsetAddToCart();
        }

        return $this;
    }

    /**
     * @return Vasilica_Glami_Model_Session
     */
    protected function getSession()
    {
        return Mage::getSingleton('vasilica_glami/session');
    }

    /**
     * @return Vasilica_Glami_Helper_Data
     */
    protected function helper()
    {
        return Mage::helper("vasilica_glami");
    }
}