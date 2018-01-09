<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '标题是必填的',
            'email.required'  => 'Email是必填的',
            'email.email' => 'Email格式不正确',
            'email.unique' => 'Email已经存在',
            'password.required' => 'password必须的'
        ];
    }
}
