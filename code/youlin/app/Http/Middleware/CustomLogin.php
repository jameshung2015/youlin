<?php

namespace App\Http\Middleware;

use Closure;

class CustomLogin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authorization = $request->header('Authorization', '');
        $authValidate  = app('our_auth');
        try {
            if (env('APP_DEBUG') && !$authorization) {
                $data = [
                    'user_id'    => 10,
                    'nickname'   => '昵称',
                    'mobile'     => '手机号',
                    'created_at' => '2019-12-01',
                    'headimgurl' => 'http://thirdwx.qlogo.cn/mmopen/D10rjCF86OFCRHpgQAwYO70wSCNQ3F3qEngHgZLZqibfQXX2YE5RjibiajOLuNewQR5Ev7lG9RgX7a8kuhmLlHngzgM7fVy0gBu/132',
                ];
            } else {
                $data = $authValidate->parse($authorization);
            }

            $request->setUserResolver(function () use ($data) {
                return $data;
            });

            return $next($request);
        } catch (\Exception $ex) {
            echo json_encode(['status' => 0, 'msg' => $ex->getMessage()]);
            exit;
        }
    }
}
