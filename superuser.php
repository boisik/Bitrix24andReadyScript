<?php
require('setup.inc.php');

$bitrix = new \CrmB24\Model\Bitrix();

$result  = \RS\Orm\Request::make()
    ->select()
    ->from(new \Catalog\Model\Orm\Offer())
  //  ->where('id'=='46')
    ->objects();


foreach($result as $offer){
   // if(empty($offer['bitrix_id']))
      //  {
            $bitrix->addProduct($offer);
      //  }

    }



