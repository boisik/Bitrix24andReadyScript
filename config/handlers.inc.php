<?php

namespace CrmB24\Config;

use RS\Event\HandlerAbstract;

use \RS\Orm\Type;
/**
 * Класс содержит обработчики событий, на которые подписан модуль
 */
class Handlers extends HandlerAbstract
{
    /**
     * Добавляет подписку на события
     *
     * @return void
     */
    function init()
    {
        $this
            ->bind('orm.beforewrite.catalog-product')
            ->bind('orm.beforewrite.catalog-offer')
            ->bind('orm.init.catalog-product')
            ->bind('orm.init.catalog-offer');

    }


    public static function ormInitCatalogOffer($offer)
    {
        $offer->getPropertyIterator()->append(array(

            'bitrix_id' => new Type\Integer(array(
                'visible' => false,
                'description' => t('Идентификатор в CRM B24'),
                'default' => null,

            )),
            'bitrix_must_update' => new Type\Integer(array(
                'visible' => false,
                'description' => t('пора обновить в црмке'),
                'default' => 0,

            )),

        ));
    }


    public static function ormInitCatalogProduct($product)
    {
        $product->getPropertyIterator()->append(array(


            'bitrix_must_update' => new Type\Integer(array(
                'visible' => false,
                'description' => t('пора обновить в црмке'),
                'default' => 0,

            )),

        ));
    }

    /**
     * Обрабатывает привязку файлов при создании товара
     */
    public static function ormBeforewriteCatalogProduct($params)
    {
        $product = $params['orm'];
        if (!$product->runtimeUpdate){
             if ($product->isModified('title') || $product->isModified('short_description') || $product->isModified('public')) {
                 $product['bitrix_must_update'] = 1;
             }
         }


    }
    /**
     * Обрабатывает привязку файлов при создании товара
     */
    public static function ormBeforewriteCatalogOffer($params)
    {
        $offer = $params['orm'];
        if (!$offer->runtimeUpdate){
            if ($offer->isModified('title') || $offer->isModified('pricedata_arr')) {
                 $offer['bitrix_must_update'] = 1;
              }
         }



    }
}
