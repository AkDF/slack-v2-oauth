<?php

namespace Socialite\SlackV2\Providers;

use Laravel\Socialite\SocialiteServiceProvider as SocialiteParentServiceProvider;
use Socialite\SlackV2\Socialite\Socialite;

class SocialiteServiceProvider extends SocialiteParentServiceProvider
{
    /**
     *
     */
    public function register()
    {
        $this->app->singleton('Laravel\Socialite\Contracts\Factory', function ($app) {
            return new Socialite($app);
        });
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/../config/slack_v2.php' => config_path('slack_v2.php'),
            ],
            'slack-v2-config'
        );
    }
}