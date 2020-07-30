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
        $form = \Feedback\Model\Orm\FormItem::loadByWhere(array(
            'id' => $item->form_id
        ));

        $data = $item->tableDataUnserialized();
        $leadData = array();
        foreach ($data as $key => $FormFieldItem){
            $leadData[$key]['name'] = $FormFieldItem['field']['title'];
            $leadData[$key]['value'] = $FormFieldItem['value'];
        }

        $newLead['fields']['TITLE']     = $form['title'].' с сайта '.$_SERVER['SERVER_NAME'];
        $newLead['fields']['STATUS_ID'] = 'NEW';
        $newLead['fields']['OPENED']    = 'Y';



        $newLead['fields']['ASSIGNED_BY_ID'] = $this->config->id_lead_manager;
            foreach ($data as $key => $FormFieldItem){
                $newLead['fields']['COMMENTS'] .= $FormFieldItem['field']['title'].' : '.$FormFieldItem['value'].'<br>';

            }

        Log::write('Экспорт Лида');


        $response = $this->requestToCRM($newLead,self::ADD_LEAD_REQ);

        if ($response['result']){
            Log::write('Экспортирован _'.$response['result']);
            $item['bitrix_id'] = $response['result'];
            $item->update();

        }


    }





}