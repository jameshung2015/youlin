<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2020/5/17 2:34
 */

namespace App\Http\Controllers\Second;

use App\Http\Controllers\Controller;
use App\Models\MonthStatistics;
use App\Models\User;
use App\Models\UserApplyTime;
use App\Models\UserTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeController extends Controller
{

    private function makeData($users, $data)
    {
        $result = [];

        foreach ($data as $datum) {
            $user     = $users->get($datum->user_id);
            $result[] = [
                'user_id'  => $datum->user_id,
                'amount'   => $datum->amount,
                'nickname' => $user->nickname,
                'head'     => $user->headimgurl,
            ];
        }

        return $result;
    }

    /**
     * 数据统计
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistic(Request $request)
    {
        $start = now()->firstOfMonth()->toDateString();
        $end   = now()->toDateString();

        $self = MonthStatistics::selfTeam($start, $end, $this->userId());
        $hot  = MonthStatistics::hotSelfTeam($start, $end, $this->userId());

        $live  = MonthStatistics::live($start, $end);
        $click = MonthStatistics::click($start, $end);
        $user  = User::query()->whereIn('user_id', $live->pluck('user_id')->merge($click->pluck('user_id')))->get()->keyBy('user_id');

        #排名前三的点击文章
        $hotActive = UserTime::query()->with(['modelable', 'attachment', 'apply'])
            ->orderByDesc('click')
            ->limit(3)
            ->get(['id', 'model_able', 'model_id', 'user_id', 'source', 'click', 'party', 'transmit', 'comment', 'open_type', 'created_at']);

        # 好文分享前五的文章
        $good = UserTime::query()->with(['modelable', 'attachment', 'apply'])
            ->orderByDesc('transmit')
            ->limit(5)
            ->get(['id', 'model_able', 'model_id', 'user_id', 'source', 'click', 'party', 'transmit', 'comment', 'open_type', 'created_at']);

        $result = [
            'hot_total'   => MonthStatistics::hotTotal($start, $end),
            'hot_part'    => $hot->isEmpty() ? 0 : $hot->first()->amount,
            'part_person' => MonthStatistics::person($start, $end), #总参与人数
            'self_party'  => $self->isEmpty() ? 0 : $self->first()->amount,
            'live'        => $this->makeData($user, $live),
            'click'       => $this->makeData($user, $click),
            'good'        => UserTime::formatUserTime($good, false, $good->pluck('user_id')->all()),
            'hot_active'  => UserTime::formatUserTime($hotActive, false, $hotActive->pluck('user_id')->all()),
        ];

        return $this->success($result);
    }

    //活动详情
    public function show(Request $request)
    {
        $time = UserTime::query()->with(['modelable', 'attachment', 'apply'])->find($request->input('id', 0));

        $time->click += 1;
        $time->save();

        return $this->success(UserTime::formatUserTime([$time], true, [$time->user_id]));
    }

    // 时光列表页 这只是我发布的
    public function index(Request $request)
    {
        $search  = $request->input('searchVal');
        $userId  = $this->userId();
        $user    = User::query()->find($userId);
        $address = $request->input('address', '');
        $count   = UserTime::who($userId)->when($search, function (Builder $builder, $value) {
            $builder->where('title', 'like', "%{$value}%");
        })->when($address, function (Builder $builder, $value) {
            $builder->where('address', 'like', "%{$value}%");
        })->count();

        if (!$count) {
            return $this->success(['data' => null, 'created' => $user->created_at->toDateTimeString()]);
        }

        $userTimes = UserTime::who($userId, ['modelable', 'attachment', 'apply'])
            ->when($search, function (Builder $builder, $value) {
                $builder->where('title', 'like', "%{$value}%");
            })->when($address, function (Builder $builder, $value) {
                $builder->where('address', 'like', "%{$value}%");
            })
            ->orderByDesc('id')
            ->forPage($request->input('page', 1), 20)
            ->get(['id', 'model_able', 'model_id', 'user_id', 'source', 'click', 'party', 'transmit', 'comment', 'open_type', 'created_at']);

        return $this->success([
            'times'   => UserTime::formatUserTime($userTimes, true, $userTimes->pluck('user_id')->all()),
            'created' => $user->created_at->toDateTimeString(),
        ]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return $this->error('请选择要删除的活动');
        }

        $time = UserTime::query()->where('id', $id)->where('user_id', $this->userId())->first();

        if (!$time) {
            return $this->error('仅能删除自己的活动');
        }

        DB::transaction(function () use ($time, $id) {
            // 删除评论
            //  $time->comments()->delete();
            // 删除参与者
            UserApplyTime::query()->where('time_id', $id)->delete();
            // 删除活动
            $time->delete();
            // 统计也要删掉
        });

        return $this->success();
    }
}
