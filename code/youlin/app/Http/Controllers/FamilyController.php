<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\MemberShip;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserFamilyResource;
use Illuminate\Support\Facades\DB;

class FamilyController extends Controller
{

    public function del(Request $request)
    {
        $user     = $request->user();
        $familyId = $request->get('family_id', 0);
        $family   = Family::query()->whereKey($familyId)->where('user_id', $user['user_id'])->first();
        if (!$family) {
            return $this->success();
        }


        // 删除群组成员。最后删除群组
        FamilyMember::query()->where('family_id', $familyId)->delete();
        $family->delete();
        return $this->success();
    }

    public function addFamilyMember(Request $request)
    {
        $familyId = $request->post('family_id', 0);
        $member   = $request->post('member', []);
        if (empty($familyId) || empty($member)) {
            return $this->error('No information');
        }

        $user   = $request->user();
        $family = Family::query()->whereKey($familyId)->where('user_id', $user['user_id'])->first();
        if (!$family) {
            return $this->error('No  Exit');
        }

        $member = MemberShip::query()->where('user_id', $user['user_id'])->whereIn('member_id', $member)->pluck('member_id');
        if ($member->isEmpty()) {
            return $this->error('请选择家庭成员');
        }

        foreach ($member as $item) {
            FamilyMember::query()->firstOrCreate([
                'family_id' => $familyId,
                'member_id' => $item,
            ]);
        }
        return $this->success();
    }

    public function deleted(Request $request)
    {
        $user = $request->user();
        $id   = $request->get('family_id', 0);
        return $this->responseWithResult(Family::query()->whereKey($id)->where('user_id', $user['user_id'])->delete());
    }

    public function subMember(Request $request)
    {
        $member   = $request->post('member', []);
        $familyId = $request->post('family_id', 0);
        if (empty($familyId) || empty($member)) {
            return $this->error('No information');
        }

        $user   = $request->user();
        $family = Family::query()->whereKey($familyId)->where('user_id', $user['user_id'])->first();
        if (!$family) {
            return $this->error('No  Exit');
        }

        $condition = ['family_id' => $familyId];
        return $this->responseWithResult(FamilyMember::query()->where($condition)->whereIn('member_id', $member)->delete());
    }

    public function FamilyRename(Request $request)
    {
        $user   = $request->user();
        $family = $request->post();

        $familyModel = Family::query()
            ->whereKey($family['family_id'])
            ->where('user_id', $user['user_id'])
            ->first();
        if (!$familyModel) {
            return $this->error('No  Exit');
        }

        if (trim($family['family_name']) === $familyModel->family_name) {
            return $this->error('No change');
        }

        $familyModel->family_name = trim($family['family_name']);
        return $this->responseWithResult($familyModel->save());
    }

    public function add(Request $request)
    {
        $user   = $request->user();
        $family = $request->post();

        if (empty($family['family_name'])) {
            return $this->error('No Family Name');
        }

        $model              = new Family();
        $model->user_id     = $user['user_id'];
        $model->family_name = trim($family['family_name']);
        $model->save();

        if (is_array($family['member']) && count($family['member'])) {
            $member = MemberShip::query()->whereIn('member_id', $family['member'])->pluck('member_id');
            if ($member->isNotEmpty()) {
                $familyId = $model->family_id;
                $insert   = array_map(function ($member) use ($familyId) {
                    return [
                        'member_id'  => $member,
                        'family_id'  => $familyId,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }, $member->toArray());
                FamilyMember::query()->insert($insert);
            }
        }

        return $this->success(['family_id' => $model->family_id]);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        return new UserFamilyResource(User::query()->with('memberShip:user_id,member_id,remark_name')->find($user['user_id']));
    }

    // 邀请用户成为 亲人 -- 获取用户信息的地方是否也要处理
    public function addMember(Request $request)
    {
        try {
            $user = $request->user();
            // 邀请人
            $invitation = $request->get('invitation', 'wx_openid');
            DB::transaction(function () use ($user, $invitation) {
                $member = User::query()->where('wx_openid', $invitation)->first();
                if (!$member) {
                    throw new \Exception('链接失效');
                }
                MemberShip::query()->firstOrCreate(['user_id' => $user['user_id'], 'member_id' => $member->user_id]);
                MemberShip::query()->firstOrCreate(['member_id' => $user['user_id'], 'user_id' => $member->user_id]);
            });
            return $this->success();
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

}
