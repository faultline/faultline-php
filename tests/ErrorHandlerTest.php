<?php

namespace Faultline\Tests;

use PHPUnit_Framework_TestCase;
use Faultline\Notifier;
use Faultline\ErrorHandler;

class ErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * testError
     *
     */
    public function testError()
    {
        $notifier = new NotifierMock([
            'project' => 'faultline-test',
            'apiKey' => 'xxxxXXXXXxXxXXxxXXXXXXXxxxxXXXXXX',
            'endpoint' => 'https://xxxxxxxxx.execute-api.ap-northeast-1.amazonaws.com/v0',
            'notifications' => [
                [
                    'type'=> 'slack',
                    'endpoint'=> 'https://hooks.slack.com/services/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',
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
        $handler = new \Faultline\ErrorHandler($notifier);
        $handler->register();

        $er = error_reporting(E_ALL | E_STRICT);
        Troublemaker::echoUndefinedVar();
        error_reporting($er);

        $notice = $notifier->notice;
        $error = $notice['errors'][0];
        $this->assertEquals('notice', $error['type']);
    }
}
