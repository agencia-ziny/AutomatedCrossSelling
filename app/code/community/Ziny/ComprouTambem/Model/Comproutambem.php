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
class Ziny_ComprouTambem_Model_Comproutambem extends Mage_Core_Model_Abstract {

    public function toOptionArray() {
        
        return array(
            array(
                'value' => 'left',
                'label' => 'Fixado na Esquerda'),
            array(
                'value' => 'right',
                'label' => 'Fixado na Direita'),
        );
    }

    public function getProductBought($productid) {

        $product = $this->getProduct($productid);
        $_order = '';
        $_productOption = array();
        $productOption = array();
        $_orderId = '';

        $_order = Mage::getResourceModel('sales/order_item_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('product_id', $product['id'])
                ->addAttributeToFilter('product_type', $product['type'])
                ->distinct(true)
                ->load();

        foreach ($_order as $order) {

            $_orderId[] = $order->getOrderId();
        }

        $_product = Mage::getResourceModel('sales/order_item_collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('order_id', $_orderId)
                ->distinct(true)
                ->load();

        foreach ($_product as $_productData) {

            $_productId[] = $_productData->getProductId();
            $_productOption[] = $_productData->getProductOptions();
        }

        foreach ($_productOption as $productOptions) {

            if (array_key_exists('super_product_config', $productOptions['info_buyRequest'])) {

                $productOption[] = $productOptions['info_buyRequest']['super_product_config']['product_id'];
            } else {
                $productOption[] = $productOptions['info_buyRequest']['product'];
            }
        }
        $_a_productId = array_diff($productOption, $product['id']);
        $_b_productId = array_unique($_a_productId);
        $_productId = array_values($_b_productId);

        return $_productId;
    }

    public function getProduct($productId) {

        $_product = Mage::getModel('catalog/product')->load($productId);
        $_productType = $_product->getTypeId();

        if ($_productType == 'grouped') {

            $product['id'][] = $productId;
            $_associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);

            foreach ($_associatedProducts as $associated) {

                $product['id'][] = $associated->getId();
            }
        } else {

            $product['id'][] = $productId;
        }

        $product['type'] = $_productType;

        return $product;
    }

}
