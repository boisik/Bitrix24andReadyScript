<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.07.2020
 * Time: 13:33
 */

namespace CrmB24\Model;
use RS\Http\Request as HttpRequest;

class LeadApi extends Bitrix
{
    const ADD_LEAD_REQ = 'crm.lead.add';



    /**
     * @param \Feedback\Model\Orm\ResultItem $item
     */
    public function addLead($item)
    {


        //$newLead['fields']['TITLE'] = $order['order_num'].' Заказ с сайта ТЕСТ '.$_SERVER['SERVER_NAME'];


        // $newOrder['fields']['ASSIGNED_BY_ID'] = $bitrixUserId;
        //$newOrder['fields']['ASSIGNED_BY_ID'] = 1 ;
        Log::write('Экспорт Лида');

        $response = $this->requestToCRM($newLead,self::ADD_LEAD_REQ);

        if ($response['result']){
            Log::write('Экспортирован _'.$response['result']);
            $item['bitrix_id'] = $response['result'];
            $item->update();

        }


    }





}