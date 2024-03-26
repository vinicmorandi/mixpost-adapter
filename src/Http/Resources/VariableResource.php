<?php

namespace SaguiAi\MixpostAdapter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariableResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'value' => $this->value,
            'default' => false,
        ];
    }
}
