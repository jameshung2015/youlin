<?php

namespace App\Http\Controllers;

use App\Models\PayLog;
use App\Models\TimeParty;
use App\Models\UserActive;
use App\Http\Requests\ActiveCreate;
use App\Models\UserTime;
use App\Services\SmallProgram\ContentSecurityService;
use App\Services\SmallProgram\SubMessageService;
use Illuminate\Support\Facades\DB;
use App\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class ActiveController extends Controller
{
    public function show(Request $request)
    {
        /**
         * @var UserTime $time
         */

        $time     = UserTime::query()->find($request->get('id'));
        $userIds  = $time->timeParty->pluck('user_id')->all();
        $nickname = User::query()->whereIn('user_id', $userIds)->pluck('nickname')->all();
        return $this->success([
            'time'    => $time->modelable,
            'part'    => $nickname,
            'userIds' => $userIds,
        ]);

    }


    /**
     * 编辑自己的活动
     *
     * @param ActiveCreate $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(ActiveCreate $request)
    {
        $timeId = $request->post('id', 0);
        $user   = $request->user();
        $time   = UserTime::query()->whereKey($timeId)->where('user_id', $user['user_id'])->where('source', 0)->first();
        if (!$time) {
            return $this->error('只能编辑自己发布的活动');
        }

        // 验证
        $params = $request->all();
        $validate = new ContentSecurityService();
        if (!$validate->validateAll([
            $params['title'], $params['content'] ?? '', $params['label'] ?? '',
        ])) {
            return $this->error('您发布的内容涉及敏感词汇，请不要上传');
        }

        try {
            DB::transaction(function () use ($user, $request, $time) {
                $time->title = $request->post('title', '');
                $time->save();

                $active = UserActive::query()->find($time->model_id);
                $active->fill($request->post());
                $active->save();

                $participant = $request->post('participant');
                if (!$participant) {
                    return;
                }

                // 处理新加入的
                $timeParty   = TimeParty::query()->where('time_id', $time->id)->pluck('user_id');
                $participant = array_diff($participant, $timeParty->all());
                if ($participant) {
                    $time->timeParty()->createMany(array_map(function ($value) {
                        return ['user_id' => $value];
                    }, $participant));
                }
            });

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error('操作失败');
        }
    }


    /**
     * 添加我的活动
     * @param ActiveCreate $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ActiveCreate $request)
    {
        $params = $request->all();
        $user   = $request->user();

        $participant   = array_unique(array_filter($params['participant']));
        $participant[] = $user['user_id'];
        if (!$participant) {
            return $this->error('没有参与人');
        }

        // 验证
        $validate = new ContentSecurityService();
        if(!$validate->validateAll([
            $params['title'], $params['content'] ?? '', $params['label'] ?? '',
        ])) {
            return $this->error('您发布的内容涉及敏感词汇，请不要上传');
        }

        try {
            DB::transaction(function () use ($user, $params, $participant) {
                $userActive          = new UserActive($params);
                $userActive->user_id = $user['user_id'];
                $userActive->save();

                $time = $userActive->times()->create([
                    'user_id'    => $user['user_id'],
                    'title'      => $params['title'],
                    'source'     => 0,
                    'type'       => Unit::TYPE_ACTIVE,
                    'model_able' => UserActive::class,
                ]);

                $time->timeParty()->createMany(array_map(function ($value) {
                    return ['user_id' => $value];
                }, $participant));

            });

            try {
                // 发生消息推送
                if (isset($params['subscribe']) && intval($params['subscribe']) > 0) {
                    $user = User::query()->whereKey($user['user_id'])->first();
                    (new SubMessageService)->sendSubActiveMessage($user->wx_openid, $params['title'], $user->nickname, $params['label'], $params['active_time']);
                }
            } catch (\Exception $ex) {

            }

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }


}
