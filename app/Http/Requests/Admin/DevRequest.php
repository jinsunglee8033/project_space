<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Role;
use Auth;

class DevRequest extends FormRequest
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
//        if ($this->id) {
//            $role = Role::findOrFail($this->id);
//
//            if ($role->name == Role::ADMIN) {
//                return [];
//            }
//        }

        return [
            'title' => ['required'],
            'type' => ['required'],
            'priority' => ['required'],
            'domain' => ['required'],
            'description' => ['required']
        ];
    }
}
