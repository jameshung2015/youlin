<?php

namespace App\Listeners;

use App\Events\NewUser;
use App\Models\User;
use App\Services\OfficialAccount\UserInformation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NewUser $event
     * @return void
     */
    public function handle($event)
    {
        // 微信公众号关注
        if ($event->type === NewUser::TYPE_OFFICIAL) {
            $service  = new UserInformation();
            $userInfo = $service->userInfo($event->params['FromUserName']);
            $user     = User::unionid($userInfo['unionid'], $userInfo['openid'])->first();
            if (!$user) {
                $user = new User();
            }

            $user->fill($userInfo);
            $user->save();
            return;

        }
    }
}
