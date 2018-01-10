<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Api\Helpers\Api\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Passport\Client;
use App\Models\User;

class LoginController extends Controller
{
    use ApiResponse, IssueTokenTrait;

    private $client;

    public function __construct()
    {
        $this->client = Client::where('password_client', 1)->first();
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->select('name', 'email', 'avatar', 'created_at')->first();
        if (empty($user)) {
            return $this->failed('找不到该用户!');
        }


        try {
            $tokens = $this->issueToken($request, 'password');
        } catch (UnauthorizedException $e) {
            return $this->failed($e->getMessage());
        }

        return $this->success(['tokens' => $tokens, 'user' => $user]);
    }
}
