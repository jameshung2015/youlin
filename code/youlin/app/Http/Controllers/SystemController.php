<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/12/9 20:57
 */


namespace App\Http\Controllers;


use App\Models\ActivePay;
use App\Models\PayLog;
use App\Models\SystemActive;
use App\Models\SystemActiveType;
use App\Models\SystemSubject;
use App\Models\SystemSubjectType;
use App\Models\User;
use App\Models\UserActive;
use App\Models\UserSubject;
use App\Models\UserTime;
use App\Services\SmallProgram\ContentSecurityService;
use App\Services\SmallProgram\SubMessageService;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{

    /**
     * 系统活动类型表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeIndex(Request $request)
    {
        $searchVal = $request->get('searchVal', '');
        $user      = $request->user();
        $data      = SystemActiveType::baseQuery()->when($searchVal, function ($query, $value) {
            $query->where('title', 'like', "%{$value}%");
        })->get()->toArray();
//        $authority = PayLog::validateAuth($user['user_id']) ? 1 : 0;

        return $this->success(array_map(function ($item) {
            return [
                'title'          => $item['title'],
                'type_id'        => $item['id'],
                'authority'      => 1,
                'background_url' => $item['url'],
            ];
        }, $data));
    }

    /**
     * 某个活动类型下的活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeAll(Request $request)
    {
        $user      = $request->user();
        $typeId    = $request->get('type', 0);
        $searchVal = $request->get('searchVal', '');
        $data      = SystemActive::type($typeId)->when($searchVal, function ($query, $value) {
            $query->where('title', 'like', "%{$value}%");
        })->get()->toArray();
        // 参与过的
        $used = UserTime::party(Unit::TYPE_ACTIVE, $user['user_id']);

        return $this->success(array_map(function ($item) use ($used, $user) {
            return [
                'title'       => $item['title'],
                'content'     => $item['content'],
                'active_id'   => $item['id'],
                'image'       => $item['image'],
                'price'       => $item['money'],
                'thumb_image' => $item['thumb_image'],
                'created_at'  => $item['created_at'],
                'authority'   => PayLog::validateAuth($user['user_id'], $item['type_id'], $item['id']) ? 1 : 0,
                'parted'      => in_array($item['id'], $used),
            ];
        }, $data));
    }

    /**
     * 活动点击
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function click(Request $request)
    {
        $id = $request->get('id', 0);

        if (!$id) {
            return $this->success();
        }
        $active        = SystemActive::query()->whereKey($id)->first();
        $active->click = $active->click + 1;
        $active->save();
        return $this->success();
    }

    /**
     * 参加系统活动
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeParty(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user        = $request->user();
                $params      = $request->all();

                // 验证
                $validate = new ContentSecurityService();
                if (!$validate->validateAll([
                    $params['title']  ?? '', $params['content'] ?? '', $params['label'] ?? '',
                ])) {
                    return $this->error('您发布的内容涉及敏感词汇，请不要上传');
                }

                $participant = array_unique(array_filter($params['participant']));
                array_push($participant, $user['user_id']);
                if (!$participant) {
                    throw new \Exception('没有参与人');
                }
                /**
                 * @var SystemActive $system
                 */
                $system = SystemActive::query()->whereKey($params['active'])->first();
                PayLog::authValidate($user['user_id'], $system->type_id, $system->id);
                $content             = $params['comment'];
                $params['title']     = $system->title;
                $params['content']   = $system->content;
                $userActive          = new UserActive($params);
                $userActive->user_id = $user['user_id'];
                $userActive->save();

                $time = $userActive->times()->create([
                    'user_id'    => $user['user_id'],
                    'title'      => $params['title'],
                    'source'     => $system->id,
                    'type'       => Unit::TYPE_ACTIVE,
                    'model_able' => UserActive::class,
                ]);

                $time->timeParty()->createMany(array_map(function ($value) {
                    return ['user_id' => $value];
                }, $participant));

                if ($content) {
                    $time->comment()->create([
                        'user_id' => $user['user_id'],
                        'content' => $content,
                    ]);
                }

                // 插入购买记录
                if ($system->money > 0) {
                    ActivePay::query()->create(['user_id'=> $user['user_id'], 'active_id' => $system->id]);
                }

                try {
                    if (isset($params['subscribe']) && intval($params['subscribe']) > 0) {
                        $user = User::query()->whereKey($user['user_id'])->first();
                        (new SubMessageService)->sendSubActiveMessage($user->wx_openid, $params['title'], $user->nickname, $params['label'], $params['active_time']);
                    }
                } catch (\Exception $ex) {

                }
            });

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage() . $ex->getLine());
        }
    }

    public function subjectParty(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $params      = $request->post();
                $user        = $request->user();
                $participant = array_unique(array_filter($params['participant']));
                array_push($participant, $user['user_id']);

                if (!$participant) {
                    throw new \Exception('没有参与人');
                }

                // 验证
                $validate = new ContentSecurityService();
                if (!$validate->validateAll([
                    $params['title'] ?? '', $params['content'] ?? '', $params['label'] ?? '',
                ])) {
                    return $this->error('您发布的内容涉及敏感词汇，请不要上传');
                }

                $system = SystemSubject::query()->whereKey($params['subject'])->first();

                $subject            = new UserSubject();
                $subject->title     = $system->title;
                $subject->user_id   = $user['user_id'];
                $subject->content   = $system->content;
                $subject->longitude = $params['longitude'];
                $subject->latitude  = $params['latitude'];
                $subject->label     = $params['label'];
                $subject->images    = json_encode($params['images']);
                $subject->save();

                $time = $subject->times()->create([
                    'user_id'    => $user['user_id'],
                    'title'      => $system->title,
                    'source'     => $system->id,
                    'type'       => Unit::TYPE_SUBJECT,
                    'model_able' => UserSubject::class,
                ]);

                $time->timeParty()->createMany(array_map(function ($value) {
                    return ['user_id' => $value];
                }, $participant));

                if ($params['content']) {
                    $time->comment()->create([
                        'user_id' => $user['user_id'],
                        'content' => $params['content'],
                    ]);
                }

            });

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage() . $ex->getLine());
        }
    }

    public function subjectIndex(Request $request)
    {
        $typeId    = $request->get('typeId');
        $searchVal = $request->get('searchVal', '');
        $data      = SystemSubject::type($typeId)->get()->when($searchVal, function ($query, $value) {
            $query->where('title', 'like', "%{$value}%");
        })->toArray();
        $user      = $request->user();
        // 参与过的
        $used = UserTime::party(Unit::TYPE_SUBJECT, $user['user_id']);
        return $this->success(array_map(function ($item) use ($used) {
            return [
                'title'      => $item['title'],
                'content'    => $item['content'],
                'subject_id' => $item['id'],
                'created_at' => $item['created_at'],
                'parted'     => in_array($item['id'], $used),
            ];
        }, $data));
    }

    /**
     * 话题的详细信息，浏览量+ 1
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectShow(Request $request)
    {
        $id      = $request->get('subjectId');
        $subject = SystemSubject::query()->whereKey($id)->first();
        if (!$subject) {
            return $this->error('没有找到您需要的活动');
        }

        $subject->click = $subject->click + 1;
        $subject->save();
        return $this->success($subject);
    }

    /**
     * 系统设置的话题列表类型
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectAll(Request $request)
    {
        $searchVal = $request->get('searchVal', '');
        $user      = $request->user();
        $authority = 1;#PayLog::validateAuth($user['user_id']) ? 1 : 0;

        $data = SystemSubjectType::baseQuery()->when($searchVal, function ($query, $value) {
            $query->where('title', 'like', "%{$value}%");
        })->get()->toArray();
        return $this->success(array_map(function ($item) use ($authority) {
            return [
                'title'     => $item['title'],
                'desc'      => $item['desc'],
                'image'     => $item['image'],
                'type_id'   => $item['id'],
                'authority' => $authority,
            ];
        }, $data));
    }
}
