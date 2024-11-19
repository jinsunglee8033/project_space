<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Actions\Fortify\PasswordValidationRules;

use App\Models\User;
use Auth;

class UserRequest extends FormRequest
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
//            'name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                'min:2',
                Rule::unique(User::class)->ignore($this->id),
            ],
            'team' => ['string'],
            'function' => ['string'],
            'role' => ['string'],
            'password' => ($this->password) ? $this->passwordRules() : [],
        ];
    }
}
