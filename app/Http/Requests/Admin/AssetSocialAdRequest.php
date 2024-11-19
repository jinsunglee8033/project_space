<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

use Auth;

class AssetSocialAdRequest extends FormRequest
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
//            'new_main_subject_line' => ['required'],
//            'new_main_preheader_line' => ['required'],
//            'new_email_blast_date' => ['required']
        ];
    }
}
