<?php

/**
 * @author Alan Barber <alan@cadence-labs.com>
 */
class Vasilica_Glami_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return Mage::getStoreConfig("vasilica_glami/page_view/enabled");
    }
    public function getApiKey()
    {
        return Mage::getStoreConfig("vasilica_glami/page_view/api_key");
    }

    public function isAddToCartEnabled()
    {
        return $this->isEnabled() &&  Mage::getStoreConfig("vasilica_glami/add_to_cart/enabled");
    }

    public function isPurchaseEnabled()
    {
        return $this->isEnabled() && Mage::getStoreConfig('vasilica_glami/purchase/enabled');
    }
    
    public function getOrderItems($order)
    {
        $data = null;
    
        if (is_numeric($order)) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($order);
        }
        
        if ($order && $order instanceof Mage_Sales_Model_Order && $order->getId()) {

            $data = [
                'item_ids' => [],
                'product_names' => [],
                'value' => round($order->getGrandTotal(), 2),
                'currency' => $order->getOrderCurrencyCode(),
                'transaction_id' => $order->getId()
            ];

            foreach ($order->getAllVisibleItems() as $item) {
                /*
                $data['item_ids'][] = $item->getProductId();
                $data['product_names'][] = $item->getName();
                */
                if ($item->getHasChildren()) {
                    foreach ($item->getChildrenItems() as $child) {
                        $data['item_ids'][] = $child->getProductId();
                        $data['product_names'][] = $child->getName();
                    }
                } else {
                    $data['item_ids'][] = $item->getProductId();
                    $data['product_names'][] = $item->getName();
                }
            }
        }
        return $data;
    }
}