<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Models\Workspace;
use SaguiAi\MixpostAdapter\Rules\HexRule;

class StoreWorkspace extends FormRequest
{
    use UsesAuth;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'hex_color' => ['required', new HexRule()]
        ];
    }

    public function handle(): null|Workspace
    {
        $workspace = null;

        DB::transaction(function () use (&$workspace) {
            $workspace = Workspace::create([
                'name' => $this->input('name'),
                'hex_color' => Str::after($this->input('hex_color'), '#')
            ]);

            $workspace->attachUser(self::getAuthGuard()->id(), WorkspaceUserRole::ADMIN);
        });

        return $workspace;
    }
}
