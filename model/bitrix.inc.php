<?php
/**
 * Created by PhpStorm.
 * User: Керчь
 * Date: 09.07.2020
 * Time: 21:29
 */

namespace CrmB24\Model;
use RS\Http\Request as HttpRequest;

class Bitrix
{
    public $config;

    function __construct()
    {
        $this->config = \RS\Config\Loader::byModule('CrmB24');
        $this->log_file = \RS\Helper\Log::file(\Setup::$PATH.\Setup::$STORAGE_DIR.'/logs/hatetrix.log');
    }

    function requestToCRM($data, $method)
    {
        $hookUrl = $this->config->crm_hook;
        $queryUrl = $hookUrl.$method.".json";

        $result = array();
        $queryData = http_build_query($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, 1);

        if (isset($response['error']) && $this->config->debug_mode) {
            $text = "\ndate: ".date('d.m.Y h:i:s A')."\nmethod: $method";
           // file_put_contents('log_error.txt', "\n". $text . print_r($response, true) . print_r($data, true), FILE_APPEND);
            $this->log_file->append($text);
            $this->log_file->append($data);
        }
        return $response;
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
        $newProduct['fields']['NAME'] =$product['title']."__Комплектация__".$offer['title'];

        $mainImageId = $offer->getMainPhotoId();
        $mainImage = new \Photo\Model\Orm\Image($mainImageId);
        $http_request = HttpRequest::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        $imageUrl = $mainImage->getOriginalUrl();
        $newProduct['fields']['PREVIEW_PICTURE'] =$request_host.$imageUrl;


       var_dump($newProduct);
        $response = $this->requestToCRM($newProduct,"crm.product.add");
        if ($response['result']){
            $offer['bitrix_id'] = $response['result'];
            $offer->update();
        }
        var_dump($response);
    }

}