<?php

/**
 * Comprou TambÃ©m
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
 * @copyright  Copyright (c) 2015 AgÃªncia Ziny (www.agenciaziny.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     AgÃªncia Ziny <dev@agenciaziny.com.br>
 */
class Ziny_ComprouTambem_Block_Comproutambem extends Mage_Catalog_Block_Product_Abstract {
    /*
     * Inicia o layout
     */

    public function _prepareLayout() {

        return parent::_prepareLayout();
    }

    /*
     * Recupera as construções
     * @var configura
     * @return varchar
     * @param $varchar
     */

    public function configura($var) {

        return Mage::getStoreConfig('catalog/comproutambem/' . $var);
    }

    /*
     * Monta comparação dos produtos
     */

    public function produtosComprados() {

        $sortProdArray = array();
        $request = $this->getRequest()->getParams();
        $productid = $request['id'];

        /*
         * Inicializa a Model
         */
        $bou = Mage::getModel('ziny_comproutambem/comproutambem');

        $orderproduct = $bou->getProductBought($productid);
        $productflag = 0;
        $prodarray = array();

        $prodecuToBeDesplay = Mage::getStoreConfig('catalog/comproutambem/colunas');
        $prodecuToBeDesplayCounter = 1;

        if (count($orderproduct) > 0) {

            $visibilidade = Mage::getStoreConfig('catalog/comproutambem/visibilidade');


            $collection = Mage::getResourceModel('catalog/product_collection');

            /*
             * Filtragem de campo
             */
            $collection->addFieldToFilter('entity_id', array('in' => $orderproduct));

            if ($visibilidade != 0) {
                /*
                 * Filtragem de Visibilidade
                 */
                $collection->addAttributeToFilter('visibility', $visibilidade);
            }

            /*
             * Filtragem de Situação
             */
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
