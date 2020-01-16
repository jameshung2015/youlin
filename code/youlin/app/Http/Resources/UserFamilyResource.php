<?php

namespace App\Http\Resources;


use App\Models\FamilyMember;
use App\Unit;

class UserFamilyResource extends BaseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $member = $this->whenLoaded('memberShip');
        return [
            'user_id'    => $this->user_id,
            'headimgurl' => $this->headimgurl,
            'nickname'   => $this->nickname,
            'mobile'     => $this->mobile,
            'memberShip' => MemberShipResource::collection($member),
            'family'     => FamilyResource::ownCollection($this->family, $member->toArray()),
        ];
    }
}
