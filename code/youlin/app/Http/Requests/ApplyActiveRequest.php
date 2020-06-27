<?php

namespace App\Http\Requests;


use App\Rules\MobileCheck;

class ApplyActiveRequest extends HttpResponseForm
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'      => 'required',
            'name'    => 'required',
            'mobile'  =>  ['required', new MobileCheck],
            'wec_hat' => 'string',
        ];
    }


    public function messages()
    {
        return [
            'id.required'    => '选择要参加的活动',
            'name.required'  => '必须填写姓名',
            'mobile'         => '请输入手机号',
            'email.email'    => '请输入正确的email',
            'wec_hat.string' => '微信号格式不正确',
        ];
    }
}
