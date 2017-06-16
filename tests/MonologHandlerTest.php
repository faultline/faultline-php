<?php

namespace Faultline\Tests;

use PHPUnit\Framework\TestCase;

class MonologHandlerTest extends TestCase
{
    public function setUp()
    {
        $this->notifier = new NotifierMock([
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

        $log = new \Monolog\Logger('billing');
        $log->pushHandler(new \Faultline\MonologHandler($this->notifier));

        Troublemaker::logAddError($log);
    }

    public function testError()
    {
        $error = $this->notifier->notice['errors'][0];
        $this->assertEquals('billing.ERROR', $error['type']);
        $this->assertEquals('charge failed', $error['message']);
    }

    public function testSeverity()
    {
        $this->assertEquals('ERROR', $this->notifier->notice['context']['severity']);
    }

    public function testBacktrace()
    {
        $backtrace = $this->notifier->notice['errors'][0]['backtrace'];
        $wanted = [[
            'file' => dirname(__FILE__).'/Troublemaker.php',
            'line' => 29,
            'function' => 'Faultline\Tests\Troublemaker::doLogAddError',
        ], [
            'file' => dirname(__FILE__).'/Troublemaker.php',
            'line' => 34,
            'function' => 'Faultline\Tests\Troublemaker::logAddError',
        ]];
        for ($i = 0; $i < count($wanted); $i++) {
            $this->assertEquals($wanted[$i], $backtrace[$i]);
        }
    }

    public function testParams()
    {
        $params = $this->notifier->notice['params'];
        $this->assertEquals([
            'monolog_context' => [
                'client_id' => 123,
            ],
        ], $params);
    }

    public function testNotifications()
    {
        $notifications = $this->notifier->notice['notifications'];
        $this->assertEquals([[
            'type'=> 'slack',
            'endpoint'=> 'https://hooks.slack.com/services/XXXXXXXXXX/B2RAD9423/WC2uTs3MyGldZvieAtAA7gQq',
            'channel'=> '#random',
            'username'=> 'faultline-notify',
            'notifyInterval'=> 5,
            'threshold'=> 10
        ], [

            'type'=> 'github',
            'userToken'=> 'XXXXXXXxxxxXXXXXXxxxxxXXXXXXXXXX',
            'owner'=> 'k1LoW',
            'repo'=> 'faultline',
            'threshold'=> -1
        ]], $notifications);
    }
}
