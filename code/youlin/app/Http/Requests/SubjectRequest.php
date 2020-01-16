<?php

namespace App\Http\Requests;


class SubjectRequest extends HttpResponseForm
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content'     => 'required',
            'title'       => 'required',
//            'longitude'   => 'required',
//            'latitude'    => 'required',
//            'label'       => 'required',
            'participant' => 'array',
            'images'      => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'content.required'     => '内容必须填写',
            'title.required'       => '活动标签必须填写',
            'active_time.required' => '活动时间必须填写',
            'longitude.required'   => '经度必须填写',
            'latitude.required'    => '纬度必须填写',
            'label.required'       => '详细地址必须填写',
            'participant.array'    => '参与人格式不正确',
            'participant.required' => '参与人必须填写',
            'images.required'      => '图片参数必须填写',
            'images.array'         => '图片参数格式不正确',
        ];
    }
}
