<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/8 2:20
 */


namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class HttpResponseForm extends FormRequest
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

    public function failedValidation(Validator $validator)
    {
        $authValidate = app('our_auth');
        $error        = $validator->errors()->all();
        throw new HttpResponseException(response()->json([
            'data'    => [],
            'message' => $error[0],
            'code'    => 0,
            'token'   => $authValidate->getToken(),
        ]));
    }
}
