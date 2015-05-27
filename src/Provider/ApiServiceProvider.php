<?php

namespace Myspace\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ApiServiceProvider implements ServiceProviderInterface
{
    protected $apiurl = 'https://apiurl/';
    protected $token  = '';
    protected $orgid  = '';
    protected $requrl = '';

    public $app;

    public function __call($name, $args)
    {
        return call_user_func_array(array($this, $name), $args);
    }

    public function register(Application $app)
    {
        $this->app = $app;
        $app['zoho'] = $this;
    }

    public function boot(Application $app) {}

    protected function get($resource, $args = array())
    {
        return $this->setUrl($resource, $args)->request();
    }

    protected function post($resource, $args = array())
    {
        return $this->setUrl($resource)->request(array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($args)
        ));
    }

    protected function request($options = array())
    {
        // init curl
        $ch = curl_init();

        // set options
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => array('Accept: application/json'),
            CURLOPT_URL            => $this->requrl,
            CURLOPT_HEADER         => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FAILONERROR    => !$this->app['debug'],
            CURLOPT_VERBOSE        => $this->app['debug']
        ) + $options);
        
        // get response
        $response = curl_exec($ch);

        if (!$response) {
            $this->app->abort(500, 'CURL Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }

        $response_info = curl_getinfo($ch);

        curl_close($ch);

        // return body
        return json_decode(substr($response, $response_info['header_size']), true);
    }

    protected function setUrl($resource, $params = array())
    {
        $this->requrl = $this->apiurl . "{$resource}?" . http_build_query(array_merge($params, array(
            'authtoken' => $this->token,
            'organization_id' => $this->orgid
        )));

        return $this;
    }
}
