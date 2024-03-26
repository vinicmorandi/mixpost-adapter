<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Models\Workspace;

class UpdateWorkspaceUserRole extends FormRequest
{
    use UsesUserModel;

    public function rules(): array
    {
        return [
            'user_id' => ['required', "exists:" . app(self::getUserClass())->getTable() . ",id"],
            'role' => ['required', Rule::in(Arr::map(WorkspaceUserRole::cases(), fn($item) => $item->value))]
        ];
    }

    public function handle()
    {
        $workspace = Workspace::firstOrFailByUuid($this->route('workspace'));

        $workspace->users()->updateExistingPivot($this->input('user_id'), ['role' => $this->input('role')]);
    }
}
