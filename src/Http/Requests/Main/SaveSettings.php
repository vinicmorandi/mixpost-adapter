<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Main;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Facades\Settings as SettingsFacade;
use SaguiAi\MixpostAdapter\Models\Setting as SettingModel;

class SaveSettings extends FormRequest
{
    use UsesAuth;

    public function rules(): array
    {
        return SettingsFacade::rules();
    }

    public function handle(): void
    {
        $schema = SettingsFacade::form();

        foreach ($schema as $name => $defaultPayload) {
            $payload = $this->input($name, $defaultPayload);

            SettingModel::updateOrCreate(['name' => $name, 'user_id' => self::getAuthGuard()->id()], ['payload' => $payload]);

            SettingsFacade::put($name, $payload);
        }
    }
}
