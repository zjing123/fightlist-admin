<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Api\Helpers\Api\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    public function register(Request $request){
        return $this->error('faild');
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password'))
        ]);

        print_r( $this->issueToken($request, 'password'));
    }
}
