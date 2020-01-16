<?php

namespace App\Http\Controllers;

use App\Models\MemberShip;
use App\Models\TimeParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    // 添加好友
    public function add(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user   = $request->user();
                $origin = $request->post('user_id');

                if ($user['user_id'] === $origin) {
                    return;
                }

                MemberShip::query()->firstOrCreate([
                    'user_id'   => $user['user_id'],
                    'member_id' => $origin,
                ]);
                MemberShip::query()->firstOrCreate([
                    'member_id' => $user['user_id'],
                    'user_id'   => $origin,
                ]);
                // 进入时光
                $time = $request->post('time', 0);
                if ($time) {
                    TimeParty::query()->firstOrCreate([
                        'user_id' => $user['user_id'],
                        'time_id' => $time,
                    ]);
                }

            });

            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function remark(Request $request)
    {
        $user   = $request->user();
        $where  = [
            'user_id'   => $user['user_id'],
            'member_id' => $request->post('user_id', '0'),
        ];
        $remark = $request->post('remark', '');
        if (empty($remark)) {
            return $this->error('请输入备注名称');
        }

        return $this->responseWithResult(MemberShip::query()->where($where)->update(['remark_name' => $remark]));
    }
}
