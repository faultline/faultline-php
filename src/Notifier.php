<?php

namespace Faultline;

use GuzzleHttp\Client;

class Notifier
{
    private $opt;

    private $client;
    
    /**
     * __construct
     *
     */
    public function __construct($opt)
    {
        if (empty($opt['project'])
            || empty($opt['apiKey'])
            || empty($opt['endpoint'])
        ) {
            throw new Exception('project,apiKey and endpoint are required');
        }
        $this->opt = $opt;
    }

    /**
     * builtNotice
     *
     * from Airbrake\Notifier::buildNotice()
     */
    public function builtNotice($exc)
    {
        $error = [
            'type' => get_class($exc),
            'message' => $exc->getMessage(),
            'backtrace' => $this->backtrace($exc),
        ];

        $context = [
            'notifier' => [
                'name' => 'faultilne-php',
                'version' => Faultline::version(),
                'url' => 'https://github.com/k1low/faultline-php',
            ],
            'os' => php_uname(),
            'language' => 'php ' . phpversion(),
        ];
        if (!empty($this->opt['appVersion'])) {
            $context['version'] = $this->opt['appVersion'];
        }
        if (!empty($this->opt['environment'])) {
            $context['environment'] = $this->opt['environment'];
        }
        if (($hostname = gethostname()) !== false) {
            $context['hostname'] = $hostname;
        }
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
            $scheme = isset($_SERVER['HTTPS']) ? 'https' : 'http';
            $context['url'] = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $context['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        $notice = [
            'errors' => [$error],
            'context' => $context,
            'environment' => $_SERVER,
        ];
        if (!empty($_REQUEST)) {
            $notice['params'] = $_REQUEST;
        }
        if (!empty($_SESSION)) {
            $notice['session'] = $_SESSION;
        }
        if (!empty($this->opt['notifications'])) {
            $notice['notifications'] = $this->opt['notifications'];
        }

        return $notice;
    }

    /**
     * sendNotice
     *
     */
    public function sendNotice($notice)
    {
        $url = $this->opt['endpoint'] . '/projects/' . $this->opt['project'] . '/errors';
        return $this->postNotice($url, $notice);
    }

    /**
     * notify
     *
     */
    public function notify($exc)
    {
        $notice = $this->builtNotice($exc);
        return $this->sendNotice($notice);
    }

    /**
     * postNotice
     *
     * from Airbrake\Notifier::backtrace()
     */
    protected function postNotice($url, $notice)
    {
        $client = new Client([
            'timeout'  => 5.0,
        ]);
        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $this->opt['apiKey']
            ],
            'json' => $notice
        ]);
        return $response->getStatusCode() === 201;
    }
    
    /**
     * backtrace
     *
     * from Airbrake\Notifier::backtrace()
     */
    private function backtrace($exc)
    {
        $backtrace = [];
        $backtrace[] = [
            'file' => $exc->getFile(),
            'line' => $exc->getLine(),
            'function' => '',
        ];
        $trace = $exc->getTrace();
        foreach ($trace as $frame) {
            $func = $frame['function'];
            if (isset($frame['class']) && isset($frame['type'])) {
                $func = $frame['class'] . $frame['type'] . $func;
            }
            if (count($backtrace) > 0) {
                $backtrace[count($backtrace) - 1]['function'] = $func;
            }

            $backtrace[] = [
                'file' => isset($frame['file']) ? $frame['file'] : '',
                'line' => isset($frame['line']) ? $frame['line'] : 0,
                'function' => '',
            ];
        }
        return $backtrace;
    }
}
