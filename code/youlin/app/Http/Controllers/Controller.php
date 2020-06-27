<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SmallProgram\SubMessageService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * 数组转xml
     * @param $data
     * @param bool $root 是否根节点
     * @return string xml
     */
    protected function arr2xml($data, $root = true)
    {
        $str = "";
        if ($root) $str .= "<xml>";
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $child = $this->arr2xml($val, false);
                $str   .= "<$key>$child</$key>";
            } else {
                $str .= "<$key><![CDATA[$val]]></$key>";
            }
        }
        if ($root) $str .= "</xml>";
        return $str;
    }

    /**
     * 如何获取用户的信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function demo(Request $request)
    {
        $authValidate = app('our_auth');
        return response()->json(['data' => $request->user(), 'token' => $authValidate->getToken()]);
    }

    protected function success($data = [], $message = '操作成功')
    {
        return $this->responseData(1, $data, $message);
    }

    protected function error($message = '操作失败', $data = [])
    {
        return $this->responseData(0, $data, $message);
    }

    protected function responseWithResult($result, $errorMsg = '失败', $successMsg = '成功', $successData = [], $errorData = [])
    {
        if ($result) {
            return $this->success($successData, $successMsg);
        } else {
            return $this->error($errorMsg, $errorData);
        }
    }

    private function responseData($code, $data, $message)
    {
        $authValidate = app('our_auth');
        return response()->json([
            'data'    => $data,
            'message' => $message,
            'code'    => $code,
            'token'   => $authValidate->getToken(),
        ]);
    }

    // 订阅活动开始消息
    protected function sendMessage($subscribe, $userId, $params)
    {
        try {
            // 发生消息推送
            if ($subscribe) {
                $user = User::query()->whereKey($userId)->first();
                (new SubMessageService)->sendSubActiveMessage($user->wx_openid, $params['title'], $user->nickname, $params['label'], $params['active_time']);
            }
        } catch (\Exception $ex) {

        }
    }

    protected function userId()
    {
        $user = request()->user();

        return  $user['user_id'];
    }
}
