<?php

namespace App\Http\Requests;

use App\Traits\HttpResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PostRequest extends FormRequest
{
    use HttpResponse;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $inUpdate =  $this->method('put') ? 'sometimes' : 'required' ;

        return [
            'title' => $inUpdate.'|array',
            'title.en' => $inUpdate.'|string',
            'title.ar' => 'sometimes|string',
            'content' => $inUpdate.'|array',
            'content.en' => $inUpdate.'|string',
            'content.ar' => 'sometimes|string',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->throwValidationException($validator);
    }
}
