<?php

namespace SaguiAi\MixpostAdapter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Util;

class UserResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->resource->relationLoaded('admin') ? ($this->admin !== null) : false,
            'workspaces' => WorkspaceResource::collection($this->whenLoaded('workspaces')),
            'settings' => $this->getSettings(),
            'created_at' => $this->created_at ? Util::dateTimeFormat($this->created_at) : null,
            'pivot' => $this->whenPivotLoaded('mixpost_workspace_user', function () {
                return [
                    'role' => $this->pivot->role,
                    'joined_at' => Util::dateTimeFormat(Carbon::parse($this->pivot->joined))
                ];
            }),
        ];
    }

    protected function getSettings()
    {
        if (!$this->resource->relationLoaded('settings')) {
            return [];
        }


        return $this->settings->pluck('payload', 'name');
    }
}
