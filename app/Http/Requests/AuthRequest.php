<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];

        switch ($this->route()->getActionMethod()) {
            case 'register':
                $rules = $this->register();
                break;
            case 'login':
                $rules = $this->login();
                break;
        }

        return $rules;
    }

    public function login(): array
    {
        return [
            'email' => 'required',
            'password' => 'required',
        ];
    }

    public function register(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ];
    }
}
