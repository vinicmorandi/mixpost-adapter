<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('users') && !$this->defaultUsersMigrationFileExists()) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable($this->passwordResetTableName()) && !$this->defaultPasswordResetTokensMigrationFileExists()) {
            Schema::create($this->passwordResetTableName(), function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        Schema::create('mixpost_user_two_factor_auth', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('secret_key');
            $table->text('recovery_codes');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('mixpost_admins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('mixpost_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('credentials');
        });

        Schema::create('mixpost_workspaces', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('hex_color', 6);
            $table->timestamps();
        });

        Schema::create('mixpost_workspace_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->foreign('workspace_id')->references('id')->on('mixpost_workspaces')->onUpdate('cascade')->onDelete('cascade');
            $table->string('role');
            $table->timestamp('joined');
            $table->unique(['workspace_id', 'user_id'], 'workspace_user_unq_id');
        });

        Schema::create('mixpost_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->string('name');
            $table->string('username')->nullable();
            $table->json('media')->nullable();
            $table->string('provider');
            $table->string('provider_id');
            $table->json('data')->nullable();
            $table->boolean('authorized')->default(false);
            $table->longText('access_token');
            $table->timestamps();

            $table->unique(['workspace_id', 'provider', 'provider_id'], 'accounts_unq_id');
        });

        Schema::create('mixpost_account_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->tinyInteger('type');
            $table->string('name');
            $table->json('data');
            $table->timestamps();
        });

        Schema::create('mixpost_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('schedule_status')->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('mixpost_post_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('mixpost_posts')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('mixpost_accounts')->onDelete('cascade');
            $table->string('provider_post_id')->nullable();
            $table->json('data')->nullable();
            $table->json('errors')->nullable();
        });

        Schema::create('mixpost_post_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('mixpost_posts')->onDelete('cascade');
            $table->bigInteger('account_id');
            $table->tinyInteger('is_original')->default(0);
            $table->json('content')->nullable();
            $table->json('options')->nullable();
        });

        Schema::create('mixpost_post_version_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('mixpost_post_versions')->onDelete('cascade');
            $table->bigInteger('media_id');
        });

        Schema::create('mixpost_posting_schedule', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->json('times');
            $table->timestamps();
        });

        Schema::create('mixpost_tags', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->string('name');
            $table->string('hex_color', 6);
            $table->timestamps();
        });

        Schema::create('mixpost_tag_post', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tag_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('mixpost_tags')->onDelete('cascade');
            $table->bigInteger('post_id')->unsigned();
            $table->foreign('post_id')->references('id')->on('mixpost_posts')->onDelete('cascade');
        });

        Schema::create('mixpost_hashtaggroups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->string('name')->nullable();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('mixpost_variables', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->string('name');
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('mixpost_templates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->string('name')->nullable();
            $table->json('content');
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('mixpost_media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->string('name');
            $table->string('mime_type');
            $table->string('disk');
            $table->string('path');
            $table->json('data')->nullable();
            $table->unsignedBigInteger('size');
            $table->unsignedBigInteger('size_total'); // including conversions
            $table->json('conversions')->nullable();
            $table->timestamps();
        });

        Schema::create('mixpost_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->json('payload');

            $table->unique(['user_id', 'name']);
        });

        Schema::create('mixpost_imported_posts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->string('provider_post_id')->index();
            $table->json('content');
            $table->json('metrics');
            $table->dateTime('created_at');

            $table->unique(['workspace_id', 'account_id', 'provider_post_id'], 'imported_posts_unq_id');
        });

        Schema::create('mixpost_facebook_insights', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->integer('type');
            $table->integer('value');
            $table->date('date');
            $table->timestamps();

            $table->unique(['workspace_id', 'account_id', 'type', 'date'], 'fb_insights_unq_id');
        });

        Schema::create('mixpost_instagram_insights', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->integer('type');
            $table->integer('value');
            $table->date('date');
            $table->timestamps();

            $table->unique(['workspace_id', 'account_id', 'type', 'date'], 'ig_insights_unq_id');
        });

        Schema::create('mixpost_pinterest_analytics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->string('provider_post_id')->index();
            $table->date('date');
            $table->json('metrics');
            $table->timestamps();

            $table->unique(['workspace_id', 'account_id', 'provider_post_id', 'date'], 'pin_analytics_unq_id');
            $table->index(['workspace_id', 'account_id'], 'pin_analytics_index');
        });

        Schema::create('mixpost_metrics', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->json('data');
            $table->date('date');

            $table->unique(['workspace_id', 'account_id', 'date'], 'metrics_unq_id');
        });

        Schema::create('mixpost_audience', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workspace_id')->unsigned()->index();
            $table->bigInteger('account_id')->unsigned()->index();
            $table->integer('total')->default(0);
            $table->date('date');

            $table->index(['workspace_id', 'account_id', 'date'], 'audience_entry_index');
        });

        Schema::create('mixpost_pages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('layout');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

        Schema::create('mixpost_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module');
            $table->json('content')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

        Schema::create('mixpost_page_block', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('mixpost_pages')->onDelete('cascade');
            $table->foreignId('block_id')->constrained('mixpost_blocks')->onDelete('cascade');
            $table->json('options')->nullable();
            $table->integer('sort_order')->nullable();
        });

        Schema::create('mixpost_configs', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('name');
            $table->json('payload')->nullable();

            $table->unique(['group', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mixpost_configs');

        Schema::dropIfExists('mixpost_page_block');
        Schema::dropIfExists('mixpost_blocks');
        Schema::dropIfExists('mixpost_pages');

        Schema::dropIfExists('mixpost_audience');
        Schema::dropIfExists('mixpost_metrics');
        Schema::dropIfExists('mixpost_pinterest_analytics');
        Schema::dropIfExists('mixpost_instagram_insights');
        Schema::dropIfExists('mixpost_facebook_insights');
        Schema::dropIfExists('mixpost_imported_posts');
        Schema::dropIfExists('mixpost_settings');
        Schema::dropIfExists('mixpost_media');
        Schema::dropIfExists('mixpost_templates');
        Schema::dropIfExists('mixpost_variables');
        Schema::dropIfExists('mixpost_hashtaggroups');

        Schema::dropIfExists('mixpost_tag_post');
        Schema::dropIfExists('mixpost_tags');

        Schema::dropIfExists('mixpost_posting_schedule');

        Schema::dropIfExists('mixpost_post_version_media');
        Schema::dropIfExists('mixpost_post_versions');
        Schema::dropIfExists('mixpost_post_accounts');
        Schema::dropIfExists('mixpost_posts');

        Schema::dropIfExists('mixpost_account_logs');
        Schema::dropIfExists('mixpost_accounts');

        Schema::dropIfExists('mixpost_workspace_user');
        Schema::dropIfExists('mixpost_workspaces');

        Schema::dropIfExists('mixpost_services');

        Schema::dropIfExists('mixpost_admins');

        Schema::dropIfExists('mixpost_user_two_factor_auth');

        if (!$this->defaultPasswordResetTokensMigrationFileExists()) {
            Schema::dropIfExists($this->passwordResetTableName());
        }

        if (!$this->defaultUsersMigrationFileExists()) {
            Schema::dropIfExists('users');
        }
    }

    private function defaultUsersMigrationFileExists(): bool
    {
        $filePath = database_path('migrations/2014_10_12_000000_create_users_table.php');

        return file_exists($filePath);
    }

    private function defaultPasswordResetTokensMigrationFileExists(): bool
    {
        $filePath = database_path("migrations/2014_10_12_100000_create_{$this->passwordResetTableName()}_table.php");

        return file_exists($filePath);
    }

    private function passwordResetTableName(): string
    {
        return Config::get('auth.passwords.users.table', 'password_reset_tokens');
    }
};
