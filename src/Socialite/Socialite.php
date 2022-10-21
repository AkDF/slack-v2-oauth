<?php

namespace Socialite\SlackV2\Socialite;

use Laravel\Socialite\SocialiteManager;

class Socialite extends SocialiteManager
{
    public function createSlackDriver()
    {
        $config = $this->container['config']['services.slack'];

        return $this->buildProvider(SlackProvider::class, $config);
    }
}