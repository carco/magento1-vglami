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
class Vasilica_Glami_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isEnabled()
    {
        return Mage::getStoreConfig("vasilica_glami/settings/enabled");
    }
    public function getApiKey()
    {
        return Mage::getStoreConfig("vasilica_glami/settings/api_key");
    }
    public function useSku()
    {
        return Mage::getStoreConfigFlag("vasilica_glami/settings/use_sku");
    }
    public function isPageViewEnabled()
    {
        return $this->isEnabled() &&  Mage::getStoreConfig("vasilica_glami/settings/page_view");
    }
    public function isAddToCartEnabled()
    {
        return $this->isEnabled() &&  Mage::getStoreConfig("vasilica_glami/settings/add_to_cart");
    }

    public function isPurchaseEnabled()
    {
        return $this->isEnabled() && Mage::getStoreConfig('vasilica_glami/settings/purchase');
    }
    
    public function getOrderItems($order)
    {
        $useSku = $this->useSku();
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
                        $data['item_ids'][] = $this->useSku() ? $child->getSku() : $child->getProductId();
                        $data['product_names'][] = $child->getName();
                    }
                } else {
                    $data['item_ids'][] = $this->useSku() ? $item->getSku() : $item->getProductId();
                    $data['product_names'][] = $item->getName();
                }
            }
        }
        return $data;
    }
    
    public function getCartItems($items) {
        
        $data = null;
        
        if ($items) {

            $data = [
                'item_ids' => [],
                'product_names' => [],
                'value' => 0.00,
                'currency' => Mage::app()->getStore()->getCurrentCurrencyCode()

            ];

            /** @var Mage_Sales_Model_Quote_Item $item */
            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                $data['item_ids'][] = $this->useSku() ? $item->getSku() : $item->getProductId();
                $data['product_names'][] = $item->getName();
                $data['value'] += $item->getProduct()->getFinalPrice() * $item->getProduct()->getQty();
            }
        }
        return $data;
    }
}