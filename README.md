# faultline-php [![Travis](https://img.shields.io/travis/faultline/faultline-php.svg)](https://travis-ci.org/faultline/faultline-php)

> [faultline](https://github.com/faultline/faultline) exception and error notifier for PHP.

## Installation

```sh
$ composer require faultline/faultline
```

## Usage

```php
// Create new Notifier instance.
$notifier = new Faultline\Notifier([
    'project' => 'faultline-php',
    'apiKey' => 'xxxxXXXXXxXxXXxxXXXXXXXxxxxXXXXXX',
    'endpoint' => 'https://xxxxxxxxx.execute-api.ap-northeast-1.amazonaws.com/v0',
    'notifications' => [
        [
            'type'=> 'slack',
            'endpoint'=> 'https://hooks.slack.com/services/XXXXXXXXXX/B2RAD9423/WC2uTs3MyGldZvieAtAA7gQq',
            'channel'=> '#random',
            'username'=> 'faultline-notify',
            'notifyInterval'=> 5,
            'threshold'=> 10,
            'timezone'=> 'Asia/Tokyo'
        ],
        [
            'type'=> 'github',
            'userToken'=> 'XXXXXXXxxxxXXXXXXxxxxxXXXXXXXXXX',
            'owner'=> 'k1LoW',
            'repo'=> 'faultline',
            'labels'=> [
                'faultline', 'bug'
            ],
            'if_exist'=> 'reopen-and-comment',
            'notifyInterval'=> 1,
            'threshold'=> 1,
            'timezone'=> 'Asia/Tokyo'
        ]
    ]
]);

// Set global notifier instance.
Faultline\Instance::set($notifier);

// Register error and exception handlers.
$handler = new Faultline\ErrorHandler($notifier);
$handler->register();

// Somewhere in the app...
try {
    throw new Exception('hello from faultline-php');
} catch(Exception $e) {
    Faultline\Instance::notify($e);
}
```

## Monolog integration

```php
$log = new Monolog\Logger('acl');
$log->pushHandler(new Faultline\MonologHandler($notifier));

$log->addError('permission denied', ['user_id' => 123]);
```

## References

- faultline-php is based on [airbrake/phpbrake](https://github.com/airbrake/phpbrake)
    - PHPBrake is licensed under [The MIT License (MIT)](https://github.com/airbrake/phpbrake/LICENSE).

## License

MIT © Ken&#39;ichiro Oyama
