<?php

namespace App\Http\Controllers\Api\V1\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\UnauthorizedException;

trait IssueTokenTrait{

    public function issueToken(Request $request, $grantType, $scope = "")
    {
        $client = new Client();

        try {
            $url = $request->root() . '/api/oauth/token';

            $params = [
                'grant_type' => $grantType,
                'client_id' => $this->client->id,
                'client_secret' => $this->client->secret,
                'scope' => $scope
            ];

            if($grantType !== 'social'){
                $params['username'] = $request->name ?: $request->email;
                $params['password'] = $request->password;
            }

            $response = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException $exception) {
            throw new UnauthorizedException('请求失败,服务器错误');
        }

        if ($response->getStatusCode() !== 401) {
            return json_decode($response->getBody()->getContents(), true);
        }

        throw new UnauthorizedException('账号和密码错误');
    }
}