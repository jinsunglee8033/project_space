<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Role;
use Auth;

class CampaignRequest extends FormRequest
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
            'campaign_brand' => ['required'],
            'retailer' => [''],
            'promotion' => ['required'],
            'name' => ['required','string', 'max:255'],
            'date_from' => ['required'],
//            'date_to' => ['required'],
            'primary_message' => ['required', 'string', 'max:255'],
            'products_featured' => ['required', 'string', 'max:255'],
            'secondary_message' => ['required', 'string', 'max:255'],
//            'asset_type' => ['required'],
            'campaign_notes' => ['required', 'string']
        ];
    }
}
