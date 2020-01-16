<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
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
                'id'      => $this->id,
                'user_id' => $this->member_id,
            ] + $this->nickname($this->member_id);
    }

    public function nickname($id)
    {
        $member = [];
        foreach (static::$member as $item) {
            if ($item['member_id'] === $id) {
                $member = $item;
                break;
            }
        }

        if ($member) {
            return ['nickname' => $member['user']['nickname'], 'remark_name' => $member['remark_name']];
        }

        return ['nickname' => '', 'remark_name' => ''];
    }


}
