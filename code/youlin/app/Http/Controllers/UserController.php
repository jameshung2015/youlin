<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2019/11/30 21:25
 */


namespace App\Http\Controllers;


use App\Http\Resources\TimeLogResource;
use App\Models\PayLog;
use App\Models\Statistics;
use App\Models\TimeComment;
use App\Models\TimeParty;
use App\Models\User;
use App\Models\UserTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 数据周统计
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function week(Request $request)
    {
        $user = $request->user();
        return $this->success(Statistics::query()->where('user_id', $user['user_id'])->orderByDesc('id')->first());
    }


    public function payment(Request $request)
    {
        $user  = $request->user();
        $count = PayLog::query()->where('user_id', $user['user_id'])->where(function ($query) {
            $query->where(function ($query) {
                $query->where('pay_type', PayLog::TYPE_ONE)->where('used', 0);
            })->orWhere(function ($query) {
                $query->where('pay_type', PayLog::TYPE_YEAR)->where('exp_time', '<=', Carbon::now()->addYear()->toDateTimeString());
            });
        })->count();

        return $count ? $this->success() : $this->error();
    }

    public function create()
    {
        $authValidate = app('our_auth');
        return $this->success($authValidate->createToken(['user_id' => '2', 'name' => '345']));
    }

    public function userInfo(Request $request)
    {
        return $this->success($request->user());
    }

    // 我的时光
    public function timeLog(Request $request)
    {
        $user  = $request->user();
        $title = $request->get('searchVal', '');
        $data  = TimeLogResource::collection(
            UserTime::query()
                ->with('modelable', 'comment', 'user')
                ->when($title, function ($query, $value) {
                    $query->where('title', 'like', "%{$value}%");
                })
                ->whereIn('id', TimeParty::query()->where('user_id', $user['user_id'])->pluck('time_id')->toArray())
                ->orderByDesc('id')
                ->get(['id', 'user_id', 'title', 'source', 'source', 'model_able', 'model_id', 'created_at', 'type'])
        );

        $user = User::query()->find($user['user_id']);
        return $this->success(['data' => $data, 'created' => $user->created_at->toDateTimeString()]);
    }

    public function comment(Request $request)
    {
        $id      = $request->post('time_id');
        $content = trim($request->post('content'));
        if (empty($id) || empty($content)) {
            return $this->error('参数不全');
        }

        $user  = $request->user();
        $model = new TimeComment([
            'time_id' => $id,
            'user_id' => $user['user_id'],
            'content' => $content,
        ]);
        return $this->responseWithResult($model->save());
    }

}
