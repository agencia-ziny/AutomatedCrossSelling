<?php

/**
 * Comprou Também
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category   Ziny
 * @package    ComprouTambem
 * @copyright  Copyright (c) 2015 Agência Ziny (www.agenciaziny.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Agência Ziny <dev@agenciaziny.com.br>
 */
class Ziny_ComprouTambem_Block_Comproutambem extends Mage_Catalog_Block_Product_Abstract {

    public function _prepareLayout() {

        return parent::_prepareLayout();
    }

    public function configura($var) {

        return Mage::getStoreConfig('catalog/comproutambem/' . $var);
    }

    public function produtosComprados() {

        $sortProdArray = array();
        $request = $this->getRequest()->getParams();
        $productid = $request['id'];
        $bou = Mage::getModel('ziny_comproutambem/comproutambem');
        $orderproduct = $bou->getProductBought($productid);
        $productflag = 0;
        $prodarray = array();
        $prodecuToBeDesplay = Mage::getStoreConfig('catalog/comproutambem/products');
        $prodecuToBeDesplayCounter = 1;

        if (count($orderproduct) > 0) {

            $collection = Mage::getResourceModel('catalog/product_collection');
            $collection->addFieldToFilter('entity_id', array('in' => $orderproduct));
            $collection->addAttributeToFilter('status', 1)->addStoreFilter();

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            
            $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
            $collection->addAttributeToSelect($attributes)
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->setPageSize($prodecuToBeDesplay);

            foreach ($collection as $_index => $_item) {

                $sortProdArray[$_index] = $_item;
            }

            $collection = $sortProdArray;
        } else {
            $collection = NULL;
        }
        return $collection;
    }

}