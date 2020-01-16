<?php

namespace App\Http\Requests;


class ActiveCreate extends HttpResponseForm
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
            'active_time' => 'required|date',
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
            'active_time.date'     => '活动时间格式不正确',
        ];
    }
}
