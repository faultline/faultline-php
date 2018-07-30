<?php

namespace Faultline;

use GuzzleHttp\Client;

class Notifier extends \Airbrake\Notifier
{
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
     * buildNotice
     *
     */
    public function buildNotice($exc)
    {
        $notice = parent::buildNotice($exc);

        $notice['context']['notifier'] = [
            'name' => 'faultilne-php',
            'version' => Faultline::version(),
            'url' => 'https://github.com/k1low/faultline-php',
        ];

        if (!empty($this->opt['notifications'])) {
            $notice['notifications'] = $this->opt['notifications'];
        }

        foreach ($notice['errors'] as $key => $error) {
            $notice['errors'][$key]['type'] = strtolower(str_replace('Airbrake\\Errors\\', '', $error['type']));
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
     * postNotice
     *
     */
    protected function postNotice($url, $notice)
    {
        $client = new Client();
        $config = [
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $this->opt['apiKey']
            ],
            'json' => $notice,
            'timeout'  => (isset($this->opt['timeout']) ? $this->opt['timeout'] : 5.0),
        ];
        if (preg_match('/^5/', \GuzzleHttp\Client::VERSION)) {
            $config['exceptions'] = false;
        } else {
            $config['http_errors'] = false;
        }
        $response = $client->post($url, $config);

        return $response->getStatusCode() === 201;
    }
}
