<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2020/5/17 0:46
 */

namespace App\Http\Controllers\Second;

use App\Http\Requests\ApplyActiveRequest;
use App\Models\DestineActive;
use App\Models\DestineActiveType;
use App\Models\SystemSubject;
use App\Models\User;
use App\Models\UserApplyTime;
use App\Models\UserTime;
use App\Unit;
use App\Models\UserActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\SmallProgram\ContentSecurityService;
use Illuminate\Support\Str;

class ActiveController extends Controller
{

    // 系统活动类型
    public function type(Request $request)
    {
        $data = DestineActiveType::open()
            ->when($request->get('searchVal', ''), function ($query, $value) {
                $query->where('title', 'like', "%{$value}%");
            })
            ->get()
            ->toArray();

        return $this->success(array_map(function ($item) {
            return [
                'title'   => $item['title'],
                'desc'    => $item['desc'],
                'image'   => $item['background_url'],
                'type_id' => $item['id'],
            ];
        }, $data));
    }

    // 某个类型下的活动
    public function list(Request $request)
    {
        $data = DestineActive::type($request->get('typeId'), $request->get('searchVal', ''))
            ->get()
            ->toArray();
        // 参与过的
        $used = UserTime::party(Unit::TYPE_ACTIVE, $this->userId());

        return $this->success(array_map(function ($item) use ($used) {
            return [
                'title'      => $item['title'],
                'content'    => $item['content'],
                'url'        => $item['url'],
                'cover_url'  => $item['cover_url'],
                'active_id'  => $item['id'],
                'created_at' => $item['created_at'],
                'parted'     => in_array($item['id'], $used),
            ];
        }, $data));
    }

    // 我的报名
    public function applyHistory()
    {
        $user  = $this->userId();
        $times = UserApplyTime::query()->where('user_id', $user)->pluck('time_id');

        if ($times->isEmpty()) {
            return $this->success(['data' => []]);
        }

        $result = UserTime::query()->with(['modelable', 'apply'])->whereIn('id', $times)->orderByDesc('id')->get();

        return $this->success(UserTime::formatUserTime($result, true, $result->pluck('user_id')->all()));
    }

    // 某个的详情
    public function show()
    {

    }

    // 申请参加活动
    public function party(ApplyActiveRequest $request)
    {
        $params = $request->input();

        $count = UserApplyTime::query()->where('time_id', $params['id'])->where('user_id', $this->userId())->count();

        if ($count > 0) {
            return $this->error('已经加入了活动');
        }

        // 插入表格
        $user  = User::query()->find($this->userId());
        $model = new UserApplyTime([
            'user_id'  => $user->user_id,
            'nickname' => $user->nickname,
            'header'   => $user->headimgurl,
            'time_id'  => $params['id'],
        ]);

        $model->name    = $params['name'];
        $model->mobile  = $params['mobile'] ?? '';
        $model->email   = $params['email'] ?? '';
        $model->we_chat = $params['we_chat'] ?? '';
        $model->save();

        return $this->success();
    }

    // 编辑我的活动
    public function update(Request $request)
    {
        $user = $request->user();

        $timeId = $request->post('id', 0);
        $time   = UserTime::query()->whereKey($timeId)->where('user_id', $user['user_id'])->first();

        if (!$time) {
            return $this->error('只能编辑自己发布的活动');
        }

        $params = $this->getParams($request->post());
        $images = $params['images'] ?? [];

        if (!is_array($images) || count($images) > 4) {
            return $this->error('最多选择四张图片');
        }

        $params['cover_url'] = $images[$params['coverIndex']] ?? '';

        $content      = $request->input('content', '');
        $introduction = $request->input('introduction', '');

        // 判断长度
        if (Str::length($content) > 1000) {
            return $this->error('内容文字长度不能超过1000个字符');
        }

        if (Str::length($introduction) > 200) {
            return $this->error('活动介绍文字长度不能超过1000个字符');
        }

        if ($images) {
            $time->attachment()->delete();
            $time->attachment()->createMany(array_map(function ($value) use ($user) {
                return ['user_id' => $user['user_id'], 'filepath' => $value];
            }, $images));
        }

        $time->title   = $params['title'];
        $time->address = $params['address'];
        $time->save();

        $time->modelable->fill($params);
        $time->modelable->save();

        return $this->success();
    }

    /**
     * 添加我的活动
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $user   = $request->user();
        $params = $this->getParams($request->post());
        $images = $params['images'] ?? [];

        if (!is_array($images) || count($images) > 4) {
            return $this->error('最多选择四张图片');
        }
        $params['cover_url'] = $images[$params['coverIndex']] ?? '';

        // 参与系统活动，标签为系统内容
        if ($params['source']) {
            $destine         = DestineActive::query()->find($params['source']);
            $params['title'] = $destine->title;
//            $params['content']   = $destine->url;
            $params['cover_url'] = $destine->cover_url;
        }

        // 判断长度
        if (!$params['title']) {
            return $this->error('请输入标签');
        }

        if (Str::length($params['content'] ?? '') > 1000) {
            return $this->error('内容文字长度不能超过1000个字符');
        }

        if (Str::length($params['introduction']) > 200) {
            return $this->error('活动介绍文字长度不能超过1000个字符');
        }

        $validate = new ContentSecurityService();
        if (!$validate->validateAll([
            $params['title'], $params['introduction'],
        ])) {
            return $this->error('您发布的内容涉及敏感词汇，请不要上传');
        }

        try {
            DB::transaction(function () use ($user, $params, $images) {
                $userActive          = new UserActive($params);
                $userActive->user_id = $user['user_id'];
                $userActive->save();

                $time = $userActive->times()->create([
                    'user_id'    => $user['user_id'],
                    'title'      => $params['title'],
                    'address'    => $params['address'],
                    'source'     => $params['source'] ?? 0,
                    'type'       => Unit::TYPE_ACTIVE,
                    'model_able' => UserActive::class,
                    'open_type'  => $params['open_type'] ?? Unit::OPEN_ALL,
                ]);

                if ($images) {
                    $time->attachment()->createMany(array_map(function ($value) use ($user) {
                        return ['user_id' => $user['user_id'], 'filepath' => $value];
                    }, $images));
                }

            });
            $this->sendMessage(isset($params['subscribe']) && intval($params['subscribe']) > 0, $user['user_id'], $params);

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }


    private function getParams($params)
    {
        $params['introduction'] = $params['introduction'] ?? '';
        $params['detail']       = $params['detail'] ?? '';
        $params['content']      = $params['content'] ?? '';
        $params['address']      = $params['address'] ?? '';

        return $params;
    }

}
