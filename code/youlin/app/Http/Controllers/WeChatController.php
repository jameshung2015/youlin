<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/26 23:51
 */

namespace App\Http\Controllers;

use App\Models\ApiLog;
use App\Models\User;
use App\Services\OfficialAccount\SendTemplateService;
use App\Services\SmallProgram\SmallUserService;
use App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use App\Events\NewUser;

class WeChatController extends Controller
{
    private $token = 'iaMncWcAkeOmX9I74O01K2X3M454B7G6';

    public function smallLogin(Request $request)
    {
        $server   = new SmallUserService();
        $userInfo = $server->loginUserInfo($request->get('code'));
        if (isset($userInfo['errmsg'])) return $this->error($userInfo['errmsg']);

        // 记录小程序登陆
        $user              = User::query()->firstOrCreate(['wx_openid' => $userInfo['openid']]);
        $user->session_key = $userInfo['session_key'];
        $user->unionid     = $userInfo['unionid'] ?? '';
        $user->save();

        $authValidate = app('our_auth');
        $authValidate->createToken([
            'user_id'    => $user->user_id,
            'created_at' => $user->created_at,
        ]);

        return $this->success(['user_id' => $user->user_id]);
    }

    /**
     * 小程序加密信息接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function smallAuth(Request $request)
    {
        $user     = $request->user();
        $findUser = User::query()->find($user['user_id']);
        if (!$findUser->unionid) {
            $server   = new SmallUserService();
            $userInfo = $server->authorization($findUser->session_key, $request->post('encryptedData'), $request->post('iv'));
            $findUser->unionid    = $userInfo['unionId'] ?? '';
        }

        $rawData = json_decode($request->post('rawData'), true);
        $findUser->nickname   = Unit::characterTransform($rawData['nickName']);
        $findUser->wx_nickname   = $rawData['nickName'];
        $findUser->headimgurl = $rawData['avatarUrl'];
        $findUser->save();

        return $this->success();
    }


    public function mobile(Request $request)
    {
        $user     = $request->user();
        $findUser = User::query()->find($user['user_id']);
        $server   = new SmallUserService();
        $userInfo = $server->authorization($findUser->session_key, $request->post('encryptedData'), $request->post('iv'));
        if (!isset($userInfo['purePhoneNumber'])) {
            return $this->error('没有获取到手机号');
        }

        $findUser->mobile = $userInfo['purePhoneNumber'];
        return $this->responseWithResult($findUser->save());
    }

    /**
     * 微信公众号 验证服务器
     * @param Request $request
     */
    public function officialAccount(Request $request)
    {
        $params = file_get_contents('php://input');
        ApiLog::logInfo('', $request->all(), file_get_contents('php://input'), '微信公众号响应', 99);
        if (!empty($params)) {
            $data = json_decode((json_encode(simplexml_load_string($params, 'SimpleXMLElement', LIBXML_NOCDATA))), true);
            switch (strtolower($data['MsgType'])) {
                case 'event':
                    if (strtolower($data['Event']) === 'subscribe') {
                        Event::dispatch(new NewUser(NewUser::TYPE_OFFICIAL, $data));
                    }

                    break;
                case 'text':
                default:
                    break;
            }

        }

        echo 'SUCCESS';
    }

    public function log(Request $request)
    {
        $service = new SendTemplateService();
        $service->testTemplate('ofldNv25fKCdam8w9OBDVBKpThfE');

//        ApiLog::logInfo($request->all(), file_get_contents('php://input'), '微信公众号测试');
    }


    /**
     * 微信公众号 验证服务器
     * @param Request $request
     * @deprecated
     */
    public function officialAccountLog(Request $request)
    {
        $signature = $request->get('signature');
        $timestamp = $request->get("timestamp");
        $nonce     = $request->get("nonce");
        $echoStr   = $request->get('echostr');

        $tmpArr = [$this->token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        echo $tmpStr === $signature ? $echoStr : '';
        die;
    }

}
