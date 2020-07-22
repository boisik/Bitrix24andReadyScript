<?php

namespace CrmB24\Config;

use RS\Event\HandlerAbstract;
use \CrmB24\Model\UserApi;
use \CrmB24\Model\OrderApi;
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
            ->bind('orm.beforewrite.feedback-resultitem')
            ->bind('orm.afterwrite.shop-order')
            ->bind('orm.beforewrite.catalog-product')
            ->bind('orm.beforewrite.catalog-offer')
            ->bind('orm.beforewrite.users-user')
            ->bind('orm.init.users-user')
            ->bind('orm.init.shop-order')
            ->bind('orm.init.catalog-product')
            ->bind('orm.init.feedback-resultitem')
            ->bind('orm.init.catalog-offer');

    }

    public static function ormBeforeWriteUsersUser($params) {


        if ($params['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG) { //Если это создание пользователя
           $userApi= new UserApi();
           $userApi->addUser($params['orm']);

        }
    }

    public static function ormAfterWriteShopOrder($params)
    {

        if (($params['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG)) { //Если это создание заявки
            $orderApi = new OrderApi();
            $orderApi->addOrder($params['orm']);
        }
    }

    public static function ormBeforeWriteFeedbackResultItem($params)
    {

        if (($params['flag'] == \RS\Orm\AbstractObject::INSERT_FLAG)) { //Если это создание заявки
            /**
             * Получаем из параметра ORM объект
             * @var \Feedback\Model\Orm\ResultItem
             */

        }
    }

    public static function ormInitFeedbackResultItem($item)
    {

        $item->getPropertyIterator()->append(array(


            'bitrix_id' => new Type\Integer(array(
                //  'visible' => false,
                'description' => t('Идентификатор в CRM B24'),
                'default' => null,

            )),

        ));
    }


    public static function ormInitCatalogOffer($offer)
    {
        $offer->getPropertyIterator()->append(array(

            'bitrix_id' => new Type\Integer(array(
               // 'visible' => false,
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

    public static function ormInitUsersUser($user)
    {
        $user->getPropertyIterator()->append(array(


            'bitrix_id' => new Type\Integer(array(
              //  'visible' => false,
                'description' => t('Идентификатор в CRM B24'),
                'default' => null,

            )),

        ));
    }

    public static function ormInitShopOrder($order)
    {
        $order->getPropertyIterator()->append(array(


            'bitrix_id' => new Type\Integer(array(
              //  'visible' => false,
                'description' => t('Идентификатор в CRM B24'),
                'default' => null,

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
