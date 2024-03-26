<?php

namespace SaguiAi\MixpostAdapter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use SaguiAi\MixpostAdapter\Util;

class PageResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'url_path' => $this->slug === 'home' ? '' : $this->slug,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'layout' => $this->layout,
            'status' => $this->status->value,
            'created_at' => Util::dateTimeFormat($this->created_at),
            'blocks' => BlockResource::collection($this->whenLoaded('blocks'))
        ];
    }
}
