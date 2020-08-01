<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 14.07.2020
 * Time: 13:33
 */

namespace CrmB24\Model;
use RS\Http\Request as HttpRequest;

class ProductApi extends Bitrix
{
    const ADD_PRODUCT_REQ = 'crm.product.add';
    const UPDATE_PRODUCT_REQ = 'crm.product.update';
    const GETLIST_PRODUCT_REQ = 'crm.product.list';
    const DELETE_PRODUCT_REQ = 'crm.product.delete';



    function createProducts()
    {
        $result = \RS\Orm\Request::make()
            ->select()
            ->from(new \Catalog\Model\Orm\Offer())
            ->where('bitrix_id IS NULL')
            ->objects();

        if (!empty($result)) {
            Log::write('Импорту подлежит_' . count($result) . "_товаров");
            foreach ($result as $offer) {
                $this->addProduct($offer);
                time_nanosleep(0, 400000000);
            }
            Log::write("__________КОНЕЦ");


        }
    }

    function updateProducts()
    {


        $result  = \RS\Orm\Request::make()
            ->select('O.*')
            ->from(new \Catalog\Model\Orm\Offer, 'O')
            ->join(new \Catalog\Model\Orm\Product(), 'O.product_id = P.id', 'P')
            ->where("(P.bitrix_must_update = 1 OR O.bitrix_must_update = 1)")

            ->objects();
       // var_dump($result);
        if (!empty($result)){
            Log::write('Обновлению подлежит_'.count($result)."_товаров");
            foreach($result as $offer){
                $this->updateProduct($offer);
                time_nanosleep(0, 400000000);
            }
        }


    }

    /**
     * @param \Catalog\Model\Orm\Offer $offer
     */

    function updateProduct($offer)
    {
        /**
         * @var \Catalog\Model\Orm\Product $product
         */
        $product = $offer->getProduct();
        $newProduct['id']= $offer['bitrix_id'];
       // $newProduct['fields']['ACTIVE'] = $product['public'] ? 'да' : 'нет';
        $newProduct['fields']['DESCRIPTION'] = strip_tags($product->short_description, '<h3><ul><li><p><br>');
        $newProduct['fields']['PRICE'] = str_replace(' ', '', $product->getCost(null,$offer->id));
        $offerTitle = ($offer['title']) ? "__Комплектация__".$offer['title']: ' ';
        $newProduct['fields']['NAME'] =$product['title'].$offerTitle;

        $mainImageId = $offer->getMainPhotoId();
        $mainImage = new \Photo\Model\Orm\Image($mainImageId);
        $http_request = HttpRequest::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        $imageUrl = $mainImage->getOriginalUrl();
        $newProduct['fields']['PREVIEW_PICTURE'] =$request_host.$imageUrl;


        Log::write('Обновление_'.$newProduct['fields']['NAME']);
        $response = $this->requestToCRM($newProduct,self::UPDATE_PRODUCT_REQ);

        if ($response['result']){
            Log::write('Обновлен _'.$offer['bitrix_id']);


            \RS\Orm\Request::make()
                ->update(new \Catalog\Model\Orm\Product())
                ->set(array('bitrix_must_update' => '0',))
                ->where(array(
                    'id' => $product->id,

                ))->exec();


            \RS\Orm\Request::make()
                ->update(new \Catalog\Model\Orm\Offer())
                ->set(array('bitrix_must_update' => '0',))
                ->where(array(
                'id' => $offer->id,

            ))->exec();



        }

    }


    /**
     * @param \Catalog\Model\Orm\Offer $offer
     */

    function addProduct($offer)
    {
        /**
         * @var \Catalog\Model\Orm\Product $product
         */
        $product = $offer->getProduct();
        $newProduct['fields']['ACTIVE'] = $product['public'] ? 'да' : 'нет';
        $newProduct['fields']['DESCRIPTION'] = strip_tags($product->short_description, '<h3><ul><li><p><br>');
        $newProduct['fields']['PRICE'] = str_replace(' ', '', $product->getCost(null,$offer->id));
        $offerTitle = ($offer['title']) ? "__Комплектация__".$offer['title']: ' ';
        $newProduct['fields']['NAME'] =$product['title'].$offerTitle;

        $mainImageId = $offer->getMainPhotoId();
        $mainImage = new \Photo\Model\Orm\Image($mainImageId);
        $http_request = HttpRequest::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        $imageUrl = $mainImage->getOriginalUrl();
        $newProduct['fields']['PREVIEW_PICTURE'] =$request_host.$imageUrl;


        Log::write('Импорт_'.$newProduct['fields']['NAME']);
        $response = $this->requestToCRM($newProduct,self::ADD_PRODUCT_REQ);
        if ($response['result']){
            Log::write('Присваивается идентификатор_'.$response['result']);
            $offer['bitrix_id'] = $response['result'];
            $offer->update();
        }

    }

    /**
     * Удаляет товары
     */

    function deleteAllProducts()
    {
        $params['order']['NAME'] = ["ASC"];
        $params['select']        = [ "ID", "NAME"];

        $response = $this->requestToCRM($params,self::GETLIST_PRODUCT_REQ);
        foreach ($response['result'] as $one){
            $this->deleteProduct($one['ID']);
            time_nanosleep(0, 400000000);
        }
        if (is_int($response['next'])){
            $this->deleteAllProducts();
        }
        Log::write('Ну, вроде бы, все.');
    }

    /**
     * Удаляет товар
     * @param string $id идентификатор товара в CRM
     */

    function deleteProduct($id)
    {

        Log::write('Удаление товара_'.$id);
        $params['id'] = $id;
        $response = $this->requestToCRM($params,self::DELETE_PRODUCT_REQ);
        if ($response['result']){
            Log::write('Удален');
        }
    }


}