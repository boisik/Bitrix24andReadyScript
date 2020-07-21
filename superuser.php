<?php
require('setup.inc.php');
$result = \RS\Orm\Request::make()
    ->select()
    ->from(new \Shop\Model\Orm\Order())
    // ->where('bitrix_id IS NULL')
    ->object();

$bitrixOrderApi = new \CrmB24\Model\OrderApi();

$resp = $bitrixOrderApi->requestToCRM(null,'crm.deal.contact.fields');
var_dump($resp);
//$bitrixOrderApi->addOrder($result);
