<?php
require('setup.inc.php');
$result = \RS\Orm\Request::make()
    ->select()
    ->from(new \Feedback\Model\Orm\ResultItem())
    // ->where('bitrix_id IS NULL')
    ->object();
var_dump($result);
$bitrixOrderApi = new \CrmB24\Model\LeadApi();

//$resp = $bitrixOrderApi->requestToCRM(null,'crm.deal.contact.fields');
//var_dump($resp);
//$bitrixOrderApi->addOrder($result);
