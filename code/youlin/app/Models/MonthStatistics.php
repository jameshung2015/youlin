<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MonthStatistics extends Model
{
    const TYPE_LIFE      = '用心生活';
    const TYPE_GOOD      = '好文排名';
    const TYPE_PERSON    = '总报名人数';
    const TYPE_TOTAL     = '我的报名人数';
    const TYPE_HOT       = '热门活动人数';
    const TYPE_HOT_COUNT = '我参与次数';

    protected $table = 'yl_month_statistics';

    protected $guarded = [];

    // 发布次数统计
    public static function live($start, $end, $limit = 3)
    {
        return UserTime::query()
            ->where('source', '=', 0)
            ->whereBetween('created_at', [$start, $end])
            ->select(['user_id', DB::raw('count(*) as amount')])
            ->groupBy('user_id')
            ->orderByDesc('amount')
            ->limit($limit)
            ->get();
    }

    public static function click($start, $end, $limit = 3)
    {
        return UserTime::query()
            ->whereBetween('created_at', [$start, $end])
            ->select(['user_id', DB::raw('sum(click) as amount')])
            ->groupBy('user_id')
            ->orderByDesc('amount')
            ->limit($limit)
            ->get();
    }

    public static function person($start, $end, $source = '=')
    {
        $send = UserTime::query()
            ->where('source', $source, 0)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_id')
            ->pluck('user_id');

        $history = UserApplyTime::query()->whereBetween('created_at', [$start, $end])->whereNotIn('user_id', $send)->groupBy('user_id')->count();

        return $history + $send->count();
    }

    // 个人参加的活动统计
    public static function selfTeam($start, $end, $userId = 0)
    {
        return UserTime::query()
            ->where('yl_user_times.source', '=', 0)
            ->whereBetween('at.created_at', [$start, $end])
            ->when($userId, function ($builder, $value) {
                $builder->where('yl_user_times.user_id', '=', $value);
            })
            ->rightJoin('yl_user_apply_times as  at', 'at.time_id', '=', 'yl_user_times.id')
            ->groupBy('yl_user_times.user_id')
            ->select(['yl_user_times.user_id', DB::raw('count(*) as amount')])
            ->get();
    }

    // 热门活动总参与人数
    public static function hotTotal($start, $end)
    {
        return UserTime::query()
            ->where('source', '!=', 0)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_id')
            ->count();
    }

    public static function hotSelfTeam($start, $end, $userId = 0)
    {
        return UserTime::query()
            ->where('source', '!=', 0)
            ->whereBetween('created_at', [$start, $end])
            ->when($userId, function ($builder, $value) {
                $builder->where('user_id', '=', $value);
            })
            ->select(['user_id', DB::raw('count(*) as amount')])
            ->groupBy('user_id')
            ->get();
    }
}
