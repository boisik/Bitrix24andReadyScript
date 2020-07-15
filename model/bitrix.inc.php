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

        if (isset($response['error'])) {
            $text = "\ndate: ".date('d.m.Y h:i:s A')."\nmethod: $method";
            Log::write($text);
            Log::write($data);
        }

        return $response;
    }



}