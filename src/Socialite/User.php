<?php
namespace Socialite\SlackV2\Socialite;

use Laravel\Socialite\Two\User as BasicUser;

class User extends BasicUser
{

    /**
     * The bot's access token.
     *
     * @var string
     */
    public string $botToken;

    /**
     * Set the token on the user.
     *
     * @param  string  $token
     * @return BasicUser
     */
    public function setBotToken(string $token): BasicUser
    {
        $this->botToken = $token;
        return $this;
    }

}