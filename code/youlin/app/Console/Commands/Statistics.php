<?php

namespace App\Console\Commands;

use App\Models\MonthStatistics;
use App\Models\User;
use App\Models\UserApplyTime;
use App\Models\UserTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Statistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistic:month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '月总结';

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
        // 统计好文分享前十名和好文分享排名前三名
        $date  = now()->subMonth()->toDateString();
        $start = now()->subMonth()->firstOfMonth()->toDateString();
        $end   = now()->firstOfMonth()->toDateString();

        $this->share($date, $start, $end);
        $this->click($date, $start, $end);
        $this->person($date, $start, $end);
        $this->party($date, $start, $end);
        $this->hotTotal($date, $start, $end);
        $this->hotCount($date, $start, $end);

        return;
    }

    protected function hotCount($date, $start, $end)
    {
        $result = MonthStatistics::hotSelfTeam($start, $end);

        foreach ($result as $item) {
            MonthStatistics::query()->create([
                'user_id'    => $item->user_id,
                'amount'     => $item->amount,
                'year_month' => $date,
                'type'       => MonthStatistics::TYPE_HOT_COUNT,
            ]);
        }
    }

    protected function hotTotal($date, $start, $end)
    {
        $result = MonthStatistics::hotTotal($start, $end);

        foreach ($result as $item) {
            MonthStatistics::query()->create([
                'user_id'    => $item->user_id,
                'amount'     => $item->amount,
                'year_month' => $date,
                'type'       => MonthStatistics::TYPE_HOT,
            ]);
        }
    }

    protected function party($date, $start, $end)
    {
        $result = MonthStatistics::selfTeam($start, $end);

        foreach ($result as $item) {
            MonthStatistics::query()->create([
                'user_id'    => $item->user_id,
                'amount'     => $item->amount,
                'year_month' => $date,
                'type'       => MonthStatistics::TYPE_TOTAL,
            ]);
        }
    }

    // 总参与人数 = 发布的人数 + 点击参与的总人数
    protected function person($date, $start, $end)
    {
        MonthStatistics::query()->create([
            'user_id'    => 0,
            'amount'     => MonthStatistics::person($start, $end),
            'year_month' => $date,
            'type'       => MonthStatistics::TYPE_PERSON,
        ]);
    }

    // 点击阅读次数
    protected function click($date, $start, $end)
    {
        $times = MonthStatistics::click($start, $end, 10);

        foreach ($times as $time) {
            MonthStatistics::query()->create([
                'user_id'    => $time->user_id,
                'amount'     => $time->amount,
                'year_month' => $date,
                'type'       => MonthStatistics::TYPE_GOOD,
            ]);
        }
    }


    // 发布次数
    protected function share($date, $start, $end)
    {
        $times = MonthStatistics::live($start, $end, 10);

        foreach ($times as $time) {
            MonthStatistics::query()->create([
                'user_id'    => $time->user_id,
                'amount'     => $time->amount,
                'year_month' => $date,
                'type'       => MonthStatistics::TYPE_LIFE,
            ]);
        }
    }

}
