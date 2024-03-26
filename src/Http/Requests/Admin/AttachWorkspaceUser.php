<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Models\Workspace;

class AttachWorkspaceUser extends FormRequest
{
    use UsesUserModel;

    public function rules(): array
    {
        return [
            'user_id' => ['required', "exists:" . app(self::getUserClass())->getTable() . ",id"],
            'role' => ['required', Rule::in(Arr::map(WorkspaceUserRole::cases(), fn($item) => $item->value))]
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => __('mixpost::user.select_user')
        ];
    }

    public function handle()
    {
        $workspace = Workspace::firstOrFailByUuid($this->route('workspace'));

        $workspace->attachUser($this->input('user_id'), WorkspaceUserRole::fromName(Str::upper($this->input('role'))));
    }
}
