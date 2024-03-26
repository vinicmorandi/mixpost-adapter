<?php

namespace SaguiAi\MixpostAdapter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlockResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'random_key' => Str::random(21),
            'name' => $this->name,
            'module' => $this->module,
            'content' => $this->content,
            'status' => $this->status
        ];
    }
}
