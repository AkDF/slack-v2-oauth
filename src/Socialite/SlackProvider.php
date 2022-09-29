<?php
namespace Socialite\SlackV2\Socialite;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;

class SlackProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * Get the access token for the given code.
     *
     * @param  string  $code
     * @return string
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'auth' => [$this->clientId, $this->clientSecret],
            'headers' => ['Accept' => 'application/json'],
            'form_params' => $this->getTokenFields($code),
        ]);

        
        return json_decode($response->getBody(), true)['access_token'];
    }

    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'channels:read',
    ];

    /**
     *
     */
    protected $userScopes = [
        'openid',
    ];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://slack.com/oauth/v2/authorize', $state);
    }

    /**
     * Get the GET parameters for the code request.
     *
     * @param  string|null  $state
     * @return array
     */
    protected function getCodeFields($state = null)
    {
        $fields = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'user_scope' => $this->formatScopes($this->getUserScopes(), $this->scopeSeparator),
            'response_type' => 'code',
        ];

        if ($this->usesState()) {
            $fields['state'] = $state;
        }

        return array_merge($fields, $this->parameters);
    }

    /**
     * Get the current user scopes.
     *
     * @return array
     */
    public function getScopes()
    {
       return config('slack_v2.scopes',$this->scopes);
    }

    /**
     * Get the current user scopes.
     *
     * @return array
     */
    public function getUserScopes()
    {
        return config('slack_v2.user_scopes',$this->userScopes);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://slack.com/api/oauth.v2.access';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $options = [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ]
        ];
        $endpoint = 'https://slack.com/api/users.identity';

        $response = $this->getHttpClient()->get($endpoint, $options)->getBody()->getContents();
        return json_decode($response, true);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException();
        }
        $response = $this->getAccessTokenResponse($this->getCode());
        $userToken = $this->getUserToken($response);
        $userData = $this->getUserByToken($userToken);
        $user = $this->mapUserToObject($userData);
        $botToken = $this->getBotToken($response);

        if(!$botToken){
           throw new \Exception('Something wrong, please try later.');
        }

        $user->setToken($userToken)
            ->setBotToken($botToken);
        return $user;
    }

    /**
     * get bot token from auth response
     */
    public function getBotToken($response)
    {
        return Arr::get($response, 'access_token');
    }

    /**
     * get user token from auth response
     */
    public function getUserToken($response)
    {
        $authedUser = Arr::get($response, 'authed_user');
        if ($authedUser) {
            return Arr::get($authedUser, 'access_token');
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new \Socialite\SlackV2\Socialite\User())->setRaw($user)->map(
            [
                'id' => Arr::get($user, 'user.id'),
                'name' => Arr::get($user, 'user.name'),
                'email' => Arr::get($user, 'user.email'),
                'avatar' => Arr::get($user, 'user.image_32'),
            ]
        );
    }
}