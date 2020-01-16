<?php

namespace App\Console\Commands;

use App\Models\MemberShip;
use App\Models\SystemActive;
use App\Models\SystemSubject;
use App\Models\TimeComment;
use App\Models\TimeParty;
use App\Models\User;
use App\Models\UserTime;
use App\Models\WeekClick;
use App\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use \App\Models\Statistics;
use Illuminate\Support\Facades\DB;

class Summary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yl_summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计数据';

    protected $nicknames = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $beginTime = Carbon::now()->subDays(7)->toDateString();
        $nowTime   = Carbon::now()->toDateString();
        //统计点击数量
        $system   = [];
        $actives  = SystemActive::query()->where('created_at', '>=', $beginTime)->get(['id', 'title', 'click']);
        $weekData = [];
        foreach ($actives as $active) {
            $weekData[] = [
                'type'       => '活动',
                'title'      => $active->title,
                'system_id'  => $active->id,
                'click'      => $active->click,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $system[]   = ['name' => $active->title, 'value' => $active->click];
        }

        $subjects = SystemSubject::query()->where('created_at', '>=', $beginTime)->get(['id', 'title', 'click']);
        foreach ($subjects as $subject) {
            $weekData[] = [
                'type'       => '话题',
                'title'      => $subject->title,
                'system_id'  => $subject->id,
                'click'      => $subject->click,
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $system[]   = ['name' => $subject->title, 'value' => $subject->click];
        }

        WeekClick::query()->insert($weekData);

        while (true) {
            $userId = Statistics::query()->where('begin', $beginTime)->where('end', $nowTime)->orderByDesc('id')->value('user_id');
            $users  = User::query()->where('user_id', '>', $userId ?? 0)->limit(100)->pluck('nickname', 'user_id');
            if ($users->isEmpty()) {
                return;
            }

            // 活动类型统计 -- 只计算自己发布的，不计算参与的
            $ownActive = UserTime::query()
                ->whereIn('user_id', $users->keys())
                ->whereBetween('created_at', [$beginTime, $nowTime])
                ->where('source', '=', 0)
                ->groupBy('user_id')
                ->select(['user_id', DB::raw('count(*) as count')])
                ->pluck('count', 'user_id');

            $systemActive = UserTime::query()
                ->whereIn('user_id', $users->keys())
                ->whereBetween('created_at', [$beginTime, $nowTime])
                ->where('source', '>', 0)
                ->groupBy('user_id', 'type')
                ->select(['user_id', DB::raw('count(*) as count'), 'type'])
                ->get();

            // 参与的活动统计
            $timeParty = TimeParty::query()
                ->whereIn('user_id', $users->keys())
                ->whereBetween('created_at', [$beginTime, $nowTime])
                ->get(['time_id', 'user_id'])
                ->groupBy('user_id');
            // 获取所有的time_id 统计
            $timeIds = [];
            foreach ($timeParty as $item) {
                $timeIds = array_merge($item->pluck('time_id')->toArray(), $timeIds);
            }
            // 统计数量
            $comments = TimeComment::query()
                ->leftJoin('yl_users', 'yl_users.user_id', '=', 'yl_time_comments.user_id')
                ->whereIn('time_id', array_unique($timeIds))
                ->whereBetween('yl_time_comments.created_at', [$beginTime, $nowTime])
                ->groupBy('time_id', 'yl_time_comments.user_id')
                ->select(['time_id', 'yl_time_comments.user_id', 'nickname', DB::raw('count(*) as count')])
                ->get()
                ->groupBy('time_id');

            $index      = 0;
            $insertData = [];
            $now        = date('Y-m-d H:i:s');

            foreach ($users as $userId => $nickname) {
                $message = [];
                $index++;
                // 评论统计
                $timeIds = $timeParty->get($userId, collect([]))->pluck('time_id');
                foreach ($timeIds as $timeId) {
                    $comment = $comments->get($timeId, []);
                    foreach ($comment as $item) {
                        $message[$item->user_id] = [
                            'value' => $item->count + (isset($message[$item->user_id]) ? $message[$item->user_id]['value'] : 0),
                            'name'  => (isset($message[$item->user_id]) ? $message[$item->user_id]['name'] : $this->nickname($userId, $item->user_id, $item->nickname)),
                        ];
                    }
                }

                // 活动类型统计
                $activeCount  = $systemActive->where('user_id', $userId)->where('type', Unit::TYPE_ACTIVE)->first();
                $subjectCount = $systemActive->where('user_id', $userId)->where('type', Unit::TYPE_SUBJECT)->first();
                $active       = [
                    ['name' => '其他', 'value' => $ownActive->get($userId, 0)],
                    ['name' => '活动', 'value' => empty($activeCount) ? 0 : $activeCount->count],
                    ['name' => '话题', 'value' => empty($subjectCount) ? 0 : $subjectCount->count],
                ];

                //点击量统计


                $insertData[] = [
                    'user_id'         => $userId,
                    'nickname'        => $nickname,
                    'begin'           => $beginTime,
                    'end'             => $nowTime,
                    'leaving_message' => json_encode(array_values($message), 256),
                    'active_type'     => json_encode($active),
                    'system_browse'   => json_encode($system),
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                if ($index >= 20) {
                    Statistics::query()->insert($insertData);
                    $insertData = [];
                    $index      = 0;
                }
            }

            Statistics::query()->insert($insertData);
        }

        return;
    }

    private function nickname($userId, $memberId, $nickname)
    {
        return MemberShip::query()
                ->where('user_id', $userId)
                ->where('member_id', $memberId)
                ->value('remark_name') ?? $nickname;
    }

}
