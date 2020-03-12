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
Class  Vasilica_Glami_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('vasilica_glami');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setAddToCart($data)
    {
        $this->setData('add_to_cart', $data);
        return $this;
    }
    
    /**
     * @param $data
     * @return $this
     */
    public function unsetAddToCart()
    {
        $this->unsetData('add_to_cart');
        return $this;
    }    

    /**
     * @return mixed|null
     */
    public function getAddToCart()
    {
        if ($this->hasAddToCart()) {
            $data = $this->getData('add_to_cart');
            $this->unsetData('add_to_cart');
            return $data;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasAddToCart()
    {
        return $this->hasData('add_to_cart');
    }

    /**
     * @return bool
     */
    public function hasInitiateCheckout()
    {
        $has = $this->hasData('initiate_checkout');
        if ($has) {
            $this->unsetData('initiate_checkout');
        }
        return $has;
    }
    
    /**
     * @return $this
     */
    public function setInitiateCheckout()
    {
        $this->setData('initiate_checkout', true);
        return $this;
    }


}