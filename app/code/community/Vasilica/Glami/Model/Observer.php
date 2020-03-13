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
        if(!$items) {
            return $this;
        }

        $data = null;
        $newData = $this->helper()->getCartItems($items);
        if ($newData) {
            $data = array_replace(array(
                'item_ids' => [],
                'product_names' => [],
                'value' => 0.00
            ), $this->getSession()->getAddToCart() ?: array());

            $data['item_ids'] = array_merge($data['item_ids'], $newData['item_ids']);
            $data['product_names'] = array_merge($data['product_names'], $newData['product_names']);
            $data['value'] = $data['value'] + $newData['value'];
            $data['currency'] = $newData['currency'];
        }


        if (!empty($data['item_ids'])) {
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