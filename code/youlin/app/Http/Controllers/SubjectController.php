<?php

namespace App\Http\Controllers;

use App\Models\TimeParty;
use App\Models\UserSubject;
use App\Models\UserTime;
use App\Services\SmallProgram\ContentSecurityService;
use Illuminate\Http\Request;
use App\Http\Requests\SubjectRequest;
use Illuminate\Support\Facades\DB;
use App\Unit;

class SubjectController extends Controller
{
    /**
     * 编辑自己的话题
     *
     * @param SubjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(SubjectRequest $request)
    {
        $params      = $request->all();
        $user        = $request->user();
        $participant = array_unique(array_filter($params['participant']));

        $time = UserTime::query()->whereKey($params['id'])->where('user_id', $user['user_id'])->where('source', 0)->first();
        if (!$time) {
            return $this->error('只能编辑自己发布的话题');
        }

        // 验证
        $validate = new ContentSecurityService();
        if (!$validate->validateAll([
            $params['title'], $params['content'] ?? '', $params['label'] ?? '',
        ])) {
            return $this->error('您发布的内容涉及敏感词汇，请不要上传');
        }

        $time->title = $params['title'];
        $time->save();

        try {
            DB::transaction(function () use ($user, $params, $participant, $time) {
                $subject = UserSubject::query()->find($time->model_id);
                if (!$subject) {
                    return;
                }

                $subject->title     = $params['title'];
                $subject->content   = $params['content'];
                $subject->longitude = $params['longitude'];
                $subject->latitude  = $params['latitude'];
                $subject->label     = $params['label'];
                $subject->images    = json_encode($params['images']);
                $subject->save();

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
            return $this->error($ex->getMessage());
        }
    }

    /**
     * 用户发布自己的话题
     * @param SubjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(SubjectRequest $request)
    {
        $params      = $request->all();
        $user        = $request->user();
        $participant = array_unique(array_filter($params['participant']));
        array_push($participant, $user['user_id']);
        if (!$participant) {
            return $this->error('没有参与人');
        }

        // 验证
        $validate = new ContentSecurityService();
        if (!$validate->validateAll([
            $params['title'], $params['content'] ?? '', $params['label'] ?? ''
        ])) {
            return $this->error('您发布的内容涉及敏感词汇，请不要上传');
        }

        try {
            DB::transaction(function () use ($user, $params, $participant) {
                $subject            = new UserSubject();
                $subject->title     = $params['title'];
                $subject->user_id   = $user['user_id'];
                $subject->content   = $params['content'];
                $subject->longitude = $params['longitude'];
                $subject->latitude  = $params['latitude'];
                $subject->label     = $params['label'];
                $subject->images    = json_encode($params['images']);
                $subject->save();

                $time = $subject->times()->create([
                    'user_id'    => $user['user_id'],
                    'title'      => $params['title'],
                    'source'     => 0,
                    'type'       => Unit::TYPE_SUBJECT,
                    'model_able' => UserSubject::class,
                ]);

                $time->timeParty()->createMany(array_map(function ($value) {
                    return ['user_id' => $value];
                }, $participant));

            });

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
}
