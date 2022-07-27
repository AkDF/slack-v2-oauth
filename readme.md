# Socialite provider for slack oauth v2

### Install the package

```sh
composer require mpociot/socialite-slack
```

### Install the Service Provider

* Remove `Laravel\Socialite\SocialiteServiceProvider` from your providers[] array in config\app.php if you have added it already.

* Add `\Socialite\SlackV2\Providers\SocialiteServiceProvider::class` to your providers[] array in config\app.php.

### Install the config

```sh
php artisan vendor:publish --tag=slack-v2-config
```

#### Append to .env

```
// other values above
SLACK_KEY=yourkeyfortheservice
SLACK_SECRET=yoursecretfortheservice
SLACK_REDIRECT_URI=https://example.com/login   
```

You do not need to add this if you add the values to the `.env` exactly as shown above. The values below are provided as a convenience in the case that a developer is not able to use the .env method

```php
'slack' => [
    'client_id' => env('SLACK_KEY'),
    'client_secret' => env('SLACK_SECRET'),
    'redirect' => env('SLACK_REDIRECT_URI'),  
], 
```


## Usage
in LoginController

redirectToProvider `\Socialite::driver('slack')->redirect();`

handleProviderCallback  `\Socialite::driver('slack')->user();`

<details>
  <summary>Next features...</summary>
Redirect to Slack with the scopes you want to access:
```php
return \Socialite::with('slack')->scopes([
	'identity.basic',
	'identity.email',
	'identity.team',
	'identity.avatar'
])->redirect();
```
</details>


## License

MIT :)