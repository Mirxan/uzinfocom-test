<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
            case 'store':
                $rules = $this->validateFile();
                break;
            case 'multipleStore':
                $rules = $this->validateMultipleFiles();
                break;
        }

        return $rules;
    }

    public function validateFile(): array
    {
        return [
            'file' => [
                'required', 'file'
            ],
        ];
    }

    public function validateMultipleFiles(): array
    {
        return [
            'files' => [
                'required', 'array',
            ],
            'files.*' => [
                'required', 'file'
            ],
        ];
    }
}
