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
    const ADD_CONTACT_TO_ORDER_REQ = 'crm.deal.contact.add';


    /**
     * @param \Shop\Model\Orm\Order $order
     */
    public function addOrder($order)
    {

        $newOrder['fields']['TITLE'] = $order['order_num'].' Заказ с сайта '.$_SERVER['SERVER_NAME'];
        $newOrder['fields']['STAGE_ID'] = "NEW";
        $newOrder['fields']['TYPE_ID'] = "GOODS";
        $newOrder['fields']['OPENED'] = "Y";
        $newOrder['fields']['OPPORTUNITY'] = $order['totalcost'];
        $newOrder['fields']['UTM_CAMPAIGN'] = $order['utm_campaign'];
        $newOrder['fields']['UTM_CONTENT'] = $order['utm_content'];
        $newOrder['fields']['UTM_TERM'] = $order['utm_term'];
        $newOrder['fields']['UTM_SOURCE'] = $order['utm_source'];
        $newOrder['fields']['UTM_MEDIUM'] = $order['utm_medium'];
        $newOrder['fields']['SOURCE_ID'] = 'STORE';
		$newOrder['fields']['UF_CRM_1596533619467'] = $order['order_num'];
		$newOrder['fields']['SOURCE_DESCRIPTION'] = 'Интернет-магазин '.$_SERVER['SERVER_NAME'];
		$newOrder['params']['REGISTER_SONET_EVENT'] = "Y";
		
		if (empty($order['utm_source'])){
			$source=$order->getSource();
			$type=$source->getType();
			$newOrder['fields']['UTM_SOURCE'] = $type['title'];
		}

        $delivery = $order->getDelivery();
        $payment = $order->getPayment();
        $address = $order->getAddress();
		
		if(isset($address['addr_address']) && $address['addr_address']!='-'){
			$customAddres =$address['addr_address'];
		}elseif(isset($address['city']) && $address['city']!='-'){
			$customAddres =$address['city'];		
		}elseif(isset($address['address']) && $address['address']!='-'){
			$customAddres =$address['address'];
		}	
		
        
        $newOrder['fields']['COMMENTS'].=
            'Доставка  : '.$delivery['title'].
            '<br>'.'Адрес :'.$customAddres.
            '<br>'.'Оплата :'.$payment['title'];


        $newOrder['fields']['ASSIGNED_BY_ID'] = $this->config->id_deal_manager;

            Log::write('Экспорт заказа '.$order['num']);

        $response = $this->requestToCRM($newOrder,self::ADD_ORDER_REQ);
 
        if ($response['result']){
            Log::write('Экспортирован _'.$response['result']);
            $order['bitrix_id'] = $response['result'];
            $order->update();

        }

        $this->addProducts($order);
        $this->addContact($order);
    }


    /**
     * @param \Shop\Model\Orm\Order $order
     */
    public function addContact($order)
    {

         $user = $order->getUser();
		
       if (!isset($user['bitrix_id'])){
		   
           $userApi = new UserApi();
           $bitrixUserId = $userApi->addUser($user);
       }else{
           $bitrixUserId =   $user['bitrix_id'];
       }
       $userInfo = array();
        $userInfo['id'] =$order['bitrix_id'] ;
        $userInfo['fields']['CONTACT_ID'] = (integer)$bitrixUserId;
        Log::write('Добавление контакта к заказу '.$order['bitrix_id']);

        $response = $this->requestToCRM($userInfo,self::ADD_CONTACT_TO_ORDER_REQ);



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
            $productInfo['PRICE'] = $cartitem['single_cost'] - $cartitem['discount'];
            $productInfo['QUANTITY'] = $cartitem['amount'];
            $productInfo["DISCOUNT_SUM"] = $cartitem['discount'];

            $productsInfo['rows'][]= $productInfo;

        }
        $items = $order->getcart()->getCartItemsByType('commission');
        if ($items){
            foreach($items as $uniq=>$item){
                $comission['PRODUCT_NAME'] = $item['title'];
                $comission['PRICE'] = $item['price'];
                $productsInfo['rows'][] = $comission;
            }
        }
		
		$itemsC = $order->getcart()->getCartItemsByType('coupon');	
		
			
		
        if ($itemsC){
            foreach($itemsC as $uniq=>$item){
				 $obj = new \Shop\Model\Orm\Discount($item['entity_id']);
				 echo("<pre>");
		
                $coupon['PRODUCT_NAME'] = "В заказе присутствует  ".$item['title']."  ".$obj['discount']."  ".$obj['discount_type'];
               
                $productsInfo['rows'][] = $coupon;
            }
        }	


        Log::write('Добавление товаров к заказу '.$order['bitrix_id']);

        $response = $this->requestToCRM($productsInfo,self::ADD_PRODUCT_TO_ORDER_REQ);


    }


}