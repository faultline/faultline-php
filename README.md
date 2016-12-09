# faultline-php [![Travis](https://img.shields.io/travis/k1LoW/faultline-php.svg)](https://travis-ci.org/k1LoW/faultline-php)

> [faultline](https://github.com/k1LoW/faultline) exception and error notifier for PHP.

## Installation

```sh
$ composer require k1low/faultline
```

## Usage

```php
// Create new Notifier instance.
$notifier = new Faultline\Notifier([
    'project' => 'faultline-test',
    'apiKey' => 'xxxxXXXXXxXxXXxxXXXXXXXxxxxXXXXXX',
    'endpoint' => 'https://xxxxxxxxx.execute-api.ap-northeast-1.amazonaws.com/v0',
    'notifications' => [
        [
            'type'=> 'slack',
            'endpoint'=> 'https://hooks.slack.com/services/XXXXXXXXXX/B2RAD9423/WC2uTs3MyGldZvieAtAA7gQq',
            'channel'=> '#random',
            'username'=> 'faultline-notify',
            'notifyInterval'=> 5,
            'threshold'=> 10
        ],
        [
            'type'=> 'github',
            'userToken'=> 'XXXXXXXxxxxXXXXXXxxxxxXXXXXXXXXX',
            'owner'=> 'k1LoW',
            'repo'=> 'faultline',
            'threshold'=> -1
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
    throw new Exception('hello from phpbrake');
} catch(Exception $e) {
    Faultline\Instance::notify($e);
}
```


## References

- [airbrake/phpbrake](https://github.com/airbrake/phpbrake)
    - PHPBrake is licensed under [The MIT License (MIT)](https://github.com/airbrake/phpbrake/LICENSE).

## License

MIT © Ken&#39;ichiro Oyama
