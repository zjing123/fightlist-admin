<?php

namespace App\Libaries\Baidu;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Translation
{
    private $url = '';

    private $appId = '';

    private $secKey = '';

    private $salt = 0;

    private $form = '';

    private $to = '';

    public function __construct($url = '', $appId = '', $secKey = '')
    {
        $this->url = $url ? $url : env('BAIDU_TRANSLATE_URL', '');
        $this->appId = $appId ? $appId : env('BAIDU_APP_ID', '');
        $this->secKey = $secKey ? $secKey : env('BAIDU_SEC_KEY', '');
        $this->setSalt();
    }

    public function translate($query, $form, $to)
    {
        $options = [
            'q' => $query,
            'appid' => $this->appId,
            'salt' => $this->getSalt(),
            'from' => $form,
            'to' => $to,
            'sign' => $this->generateSign($query)
        ];

        try{
            $client = new Client();
            $response = $client->request('POST', $this->url, $options);
        } catch (RequestException $e) {
            print_r($e);
        }

        print_r($options);
        print_r(json_decode($response->getBody()->getContents(), true));
        exit;
    }

    private function generateSign($query)
    {
        $buildString = $this->appId . $query . $this->getSalt() . $this->secKey;
        $sign = md5($buildString);
        return $sign;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    private function setSalt()
    {
        $this->salt = rand(10000, 99999);
        return $this;
    }
}