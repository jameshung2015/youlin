<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PayLog extends Model
{
    protected $table = 'yl_pay_logs';

    const TYPE_ONE  = '一次性';
    const TYPE_YEAR = '包年';

    /**
     * 是否有权限参加活动
     * @param string $userId 用户ID
     * @param string $typeId 活动类型id
     * @param string $id 活动id
     * @throws \Exception
     */
    public static function authValidate($userId, $typeId, $id)
    {
        $payLog = static::validateAuth($userId, $typeId, $id);
        if ($payLog === true) {
            return;
        }

        if (!$payLog) {
            throw new \Exception('没有权限');
        }

        $payLog->used = $payLog->used + 1;
        $payLog->save();
    }

    public static function validateAuth($userId, $typeId, $id)
    {
        // 活动money
        $money = SystemActive::query()->whereKey($id)->value('money');
        if (empty($money * 100)) {
            return true;
        }

        // 校验一次性
        $payLog = static::query()
            ->where('user_id', $userId)
            ->where('pay_type', static::TYPE_ONE)
            ->where('pay_id', '=', $id)
            ->first();
        if (!$payLog) {
            // 校验一年期
            $payLog = static::query()
                ->where('user_id', $userId)
                ->where('pay_type', static::TYPE_YEAR)
                ->where('pay_id', '=', $typeId)
                ->where('exp_time', '>=', Carbon::now()->toDateTimeString())
                ->first();
            if (!$payLog) {
                return null;
            }
        }

        return $payLog;
    }
}
