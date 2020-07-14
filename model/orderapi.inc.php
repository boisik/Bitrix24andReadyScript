<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.07.2020
 * Time: 13:33
 */

namespace CrmB24\Model;
use RS\Http\Request as HttpRequest;

class OrderApi
{
    public $bitrix;

    function __construct()
    {
        $this->bitrix = new Bitrix();

    }

    /**
     * @param \Shop\Model\Orm\Order $order
     */
    public function addOrder($order)
    {

    }


}