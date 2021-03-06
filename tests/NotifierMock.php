<?php

namespace Faultline\Tests;

use Faultline\Notifier;

/**
 * from Airbrake\Tests\NotifierMock
 */
class NotifierMock extends Notifier
{
    public $resp = [
        'headers' => 'HTTP/1.1 201 Created',
        'data' => '{"status":"success", "errors": 1}',
    ];

    public $url;
    public $notice;

    public function postNotice($url, $notice)
    {
        $this->url = $url;
        $this->notice = $notice;
        return true;
    }
}
