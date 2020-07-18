<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.07.2020
 * Time: 13:33
 */

namespace CrmB24\Model;
use RS\Http\Request as HttpRequest;

class OrderApi extends Bitrix
{
    const ADD_ORDER_REQ = 'crm.deal.add';
    const ADD_PRODUCT_TO_ORDER_REQ = 'crm.deal.productrows.set';


    /**
     * @param \Shop\Model\Orm\Order $order
     */
    public function addOrder($order)
    {
        $newOrder['fields']['TITLE'] = "Заказ с сайта ".$order['num'];
        $newOrder['fields']['STAGE_ID'] = "NEW";
        $newOrder['fields']['TYPE_ID'] = "GOODS";
        $newOrder['fields']['OPENED'] = "Y";
        $newOrder['fields']['OPPORTUNITY'] = $order['totalcost'];
        $newOrder['fields']['BEGINDATE'] = $order['dateof'];
        $newOrder['params']['REGISTER_SONET_EVENT'] = "Y";
        $user = $order->getUser();
        if (!isset($user['bitrix_id'])){
            $userApi = new UserApi();
            $bitrixUserId = $userApi->addUser($user);
        }else{
            $bitrixUserId =   $user['bitrix_id'];
        }
        $newOrder['fields']['ASSIGNED_BY_ID'] = $bitrixUserId;

        Log::write('Экспорт заказа '.$order['num']);
        $response = $this->requestToCRM($newOrder,self::ADD_ORDER_REQ);
//var_dump($response);die();
        if ($response['result']){
            Log::write('Экспортирован _'.$response['result']);
            $order['bitrix_id'] = $response['result'];
            $order->update();

        }

        $this->addProducts($order);

    }

    /**
     * @param \Shop\Model\Orm\Order $order
     */

    public function addProducts($order)
    {
        $products= $order->getcart()->getProductItems();
        $productsInfo['id'] = $order['bitrix_id'];
        foreach($products as $uniq=>$item){
            $product  = $item['product'];
            $cartitem = $item['cartitem'];
            $offer_num = $cartitem['offer'];
            $offer = $product['offers']['items'][$offer_num];

            if (!isset($offer['bitrix_id'])){
                $productApi = new ProductApi;
                $offer['bitrix_id'] = $productApi->addProduct($offer);
            }
            $productInfo['PRODUCT_ID'] = $offer['bitrix_id'];
            $productInfo['PRICE'] = $cartitem['single_cost'];
            $productInfo['QUANTITY'] = $cartitem['amount'];


            $productsInfo['id']['rows'][]= $productInfo;

        }
        Log::write('Добавление товаров к заказу '.$order['bitrix_id']);

        $response = $this->requestToCRM($productsInfo,self::ADD_PRODUCT_TO_ORDER_REQ);

        var_dump($response);
    }


}