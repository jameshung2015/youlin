<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    public static $member;

    public static function ownCollection($resource, $member)
    {
        static::$member = $member;
        return parent::collection($resource);
    }


    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'family_id'     => $this->family_id,
            'family_name'   => $this->family_name,
            'family_member' => FamilyMemberResource::ownCollection($this->member, static::$member),
        ];
    }
}
