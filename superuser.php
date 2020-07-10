<?php
require('setup.inc.php');

$bitrix = new \CrmB24\Model\Bitrix();

$result  = \RS\Orm\Request::make()
    ->select()
    ->from(new \Catalog\Model\Orm\Product())
    ->objects();


foreach($result as $product){
    if(empty($product['bitrix_id']))
        {
            $bitrix->addProduct($product);
        }

    }



