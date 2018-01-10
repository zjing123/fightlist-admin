<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Api\Helpers\Api\ApiResponse;
use App\Http\Requests\Api\RegisterUserRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Passport\Client;
use App\Models\User;

class RegisterController extends Controller
{
    use IssueTokenTrait;
    use ApiResponse;

    private $client;

    public function __construct()
    {
        $this->client = Client::where('password_client', 1)->first();
    }

    //https://laravel-china.org/articles/6976/laravel-55-uses-passport-to-implement-auth-authentication
    //http://blog.csdn.net/duanshuiliu2017/article/details/78343408?locationNum=8&fps=1
    public function register(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password'))
        ]);

        if (empty($user)) {
            $this->failed('注册失败!');
        }

        try {
            $tokens = $this->issueToken($request, 'password');
        } catch (UnauthorizedException $e) {
            return $this->failed($e->getMessage());
        }

        return $this->success(['tokens' => $tokens, 'user' => $user]);
    }
}
