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



    /**
     * @param \Shop\Model\Orm\Order $order
     */
    public function addOrder($order)
    {
        $newOrder['fields']['TITLE'] = "Заказ с сайта ".$order['num'];
        $newOrder['fields']['STAGE_ID'] = "NEW";
        $newOrder['fields']['TYPE_ID'] = "GOODS";
        $newOrder['fields']['OPENED'] = "Y";
        $newOrder['fields']['OPPORTUNITY'] = $order->getTotalPrice();
        $newOrder['fields']['BEGINDATE'] = $order['dateof'];
        $newOrder['params']['REGISTER_SONET_EVENT'] = "Y";
        $user = $order->getUser();
        if (!isset($user['bitrix_id'])){
            $userApi = new UserApi();
            $bitrixUserId = $userApi->addUser($user);
        }
        $newOrder['fields']['ASSIGNED_BY_ID'] = $bitrixUserId;

    }


}