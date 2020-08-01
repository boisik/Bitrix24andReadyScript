<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.07.2020
 * Time: 13:33
 */

namespace CrmB24\Model;
use RS\Http\Request as HttpRequest;

class UserApi extends Bitrix
{
    const ADD_USER_REQ = 'crm.contact.add';


    /**
     * @param \Users\Model\Orm\User $user
     */
    public function addUser($user)
    {
        $newUser['fields']['NAME'] = $user->name;
        $newUser['fields']['LAST_NAME'] = $user->surname;
		  
        $newUser['fields']['PHONE'][0]['VALUE'] = $user->phone;
        $newUser['fields']['PHONE'][0]['TYPE'] = 'WORK';
        $newUser['fields']['EMAIL'][0] =  	array('VALUE' => $user->e_mail,"VALUE_TYPE"=>"WORK");
      
        Log::write('Добавление клиента_'.$newUser['fields']['NAME']."_".$newUser['fields']['LAST_NAME']);
        $response = $this->requestToCRM($newUser,self::ADD_USER_REQ);
		
        if ($response['result']){
            Log::write('Присваивается идентификатор_'.$response['result']);

            //if (isset($user->id)){
                \RS\Orm\Request::make()
                    ->update(new \Users\Model\Orm\User())
                    ->set(array('bitrix_id' => $response['result'],))
                    ->where(array(
                        'id' => $user->id,

                    ))->exec();
           // }

            return $response['result'];
        }

        return 0;
    }
}