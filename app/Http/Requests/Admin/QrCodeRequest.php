<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Actions\Fortify\PasswordValidationRules;

use App\Models\User;
use Auth;

class QrCodeRequest extends FormRequest
{
    use PasswordValidationRules;
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
            'name' => ['required'],
            'email' => ['required'],
            'qr_code_for' => ['required'],
            'brand' => ['required'],
            'department' => ['required'],
            'link_to' => ['required'],
            'date_1' => ['required'],
            'date_2' => ['required'],
            'date_3' => [],
            'information' => [],
        ];
    }
}
