<?php

namespace App\Libaries\Youdao;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Translation
{
    private $url = '';

    private $appId = '';

    private $secKey = '';

    private $salt = 0;

    public function __construct($url = '', $appId = '', $secKey = '')
    {
        $this->url = $url ? $url : env('YOUDAO_TRANSLATE_URL', '');
        $this->appId = $appId ? $appId : env('YOUDAO_APP_ID', '');
        $this->secKey = $secKey ? $secKey : env('YOUDAO_SEC_KEY', '');
        $this->setSalt();
    }

    public function translate($query, $form, $to)
    {
        $options = [
            'q' => $query,
            'appKey' => $this->appId,
            'salt' => $this->getSalt(),
            'from' => $form,
            'to' => $to,
            'sign' => $this->generateSign($query)
        ];

        try{
            $client = new Client();
            $response = $client->request(
                'POST',
                $this->url,
                [
                    'form_params' => $options
                ]
            );
        } catch (RequestException $e) {
            throw $e;
            return null;
        }

        $translate = json_decode($response->getBody()->getContents());

        if ((int)$translate->errorCode !== 0) {

            return null;
        }

        return $translate->translation;
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