<?php

namespace Faultline\Tests;

class Troublemaker // from Airbrake\Tests\Troublemaker
{
    private static function doEchoUndefinedVar()
    {
        echo $undefinedVar;
    }

    public static function echoUndefinedVar()
    {
        self::doEchoUndefinedVar();
    }

    private static function doNewException()
    {
        return new \Exception('hello');
    }

    public static function newException()
    {
        return self::doNewException();
    }

    private static function doLogAddError($log)
    {
        $log->addError('charge failed', ['client_id' => 123]);
    }

    public static function logAddError($log)
    {
        self::doLogAddError($log);
    }
}
