<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use SaguiAi\MixpostAdapter\Actions\Common\CreateMastodonApp as CreateMastodonAppAction;
use SaguiAi\MixpostAdapter\Facades\Services;
use Symfony\Component\HttpFoundation\Response;

class CreateMastodonApp extends FormRequest
{
    public function rules(): array
    {
        return [
            'server' => ['required', 'string', 'max:255'],
        ];
    }

    public function handle(): void
    {
        $serviceName = "mastodon.{$this->input('server')}";

        if (Services::get($serviceName)) {
            return;
        }

        $result = (new CreateMastodonAppAction())($this->input('server'));

        if (isset($result['error'])) {
            $errors = ['server' => [$result['error']]];

            throw new HttpResponseException(
                response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
