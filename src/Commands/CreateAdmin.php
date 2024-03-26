<?php

namespace SaguiAi\MixpostAdapter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;

class CreateAdmin extends Command
{
    use UsesUserModel;

    public $signature = 'mixpost:create-admin';

    public $description = 'Create an admin user to authenticate in to Mixpost';

    public function handle(): int
    {
        $this->alert('Create an admin user');

        $email = $this->ask('What is the email address?');

        $validator = Validator::make([
            'email' => $email,
        ], [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            $this->info('The following validation errors occurred:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if ($user = self::getUserClass()::where('email', $email)->first()) {
            if ($user->isAdmin()) {
                $this->error('This user is already a Mixpost admin!');
                return self::FAILURE;
            }

            if ($this->confirm("User $email already exists. Grant admin access to Mixpost?")) {
                $user->admin()->create();

                $this->success("You have granted admin access to $email");
                return self::SUCCESS;
            } else {
                return self::FAILURE;
            }
        }

        $name = $this->ask("What is the user's name?");
        $password = $this->secret('What is the password?');

        $validator = Validator::make([
            'name' => $name,
            'password' => $password
        ], [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $this->info('The following validation errors occurred:');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        DB::transaction(function () use ($name, $email, $password) {
            $user = self::getUserClass()::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt($password)
            ]);

            $user->admin()->create();
        });

        $this->success("Admin $email created!");
        return self::SUCCESS;
    }

    protected function success($text): void
    {
        $appUrl = config('app.url');
        $corePath = config('mixpost.core_path', 'mixpost');

        $this->info($text);
        $this->line("Visit the Mixpost UI at $appUrl/$corePath");
    }
}
