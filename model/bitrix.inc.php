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

    function addProduct($product)
    {
        $newProduct['fields']['ACTIVE'] = $product['public'] ? 'да' : 'нет';
        $newProduct['fields']['DESCRIPTION'] = strip_tags($product->description, '<h3><ul><li><p><br>');
        $newProduct['fields']['PRICE'] = $product->getCost();
        $newProduct['fields']['NAME'] =$product['title'];
        $main_image=$product->getMainImage();
        $http_request = HttpRequest::commonInstance();
        $request_host = $http_request->getProtocol() . '://' . $http_request->getDomainStr();
        $imageUrl = $main_image->getOriginalUrl();
        $newProduct['fields']['PREVIEW_PICTURE'] =$request_host.$imageUrl;
        $newProduct['fields']['XML_ID'] =$product['xml_id'];
        var_dump($newProduct);
        $response = $this->requestToCRM($newProduct,"crm.product.add");
        if ($response['result']){
            $product['bitrix_id'] = $response['result'];
            $product->update();
        }
        var_dump($response);
    }

}