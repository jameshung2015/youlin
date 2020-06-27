<?php

namespace App\Http\Controllers\Second;

use App\Http\Controllers\Controller;
use App\Models\TransmitLog;
use App\Models\UserTime;
use App\Services\SmallProgram\QrCodeService;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{

    public function transmit(Request $request)
    {
        $time = UserTime::query()->find($request->input('id'));

        if (!$time) {
            return $this->success();
        }

        $log = TransmitLog::query()->firstOrNew([
            'time_id' => $request->input('id'),
            'user_id' => $this->userId(),
        ]);

        if (!$log->id) {
            $log->save();

            $time->transmit += 1;
            $time->save();
        }

        return $this->success();
    }

    public function image(Request $request)
    {
        try {
            $id   = $request->input('id', 0);
            $time = UserTime::query()->find($id);

            if (!$time) {
                return $this->error('无效数据');
            }

            if (!$time->path) {
                $service = new QrCodeService();
                $path    = $service->youLin($id);

                $time->path = $path;
                $time->save();
            }

            return $this->success(['image' => $time->path]);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
}
