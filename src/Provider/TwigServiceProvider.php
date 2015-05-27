<?php

namespace Myspace\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\TwigServiceProvider as SilexTwig;

class TwigServiceProvider extends \Twig_Extension implements ServiceProviderInterface
{
    public $app;

    public function register(Application $app)
    {
        $this->app = $app;

        $app->register(new SilexTwig(), array(
            'twig.path' => 'views',
            'twig.options' => array(
                'cache' => $app['debug'] ? false : 'storage/cache'
            )
        ));

        $app['twig']->addExtension($this);
    }

    public function boot(Application $app) {}

    public function getName()
    {
        return 'zl';
    }

    public function getFunctions()
    {
        return array(

            // cockpit collections
            new \Twig_SimpleFunction('collections', function($name, $query = [], $array = true) {
                $items = collection($name)->find($query);
                return $array ? $items->toArray() : $items;
            }),

            new \Twig_SimpleFunction('collection', function($name, $query = []) {
                return collection($name)->findOne($query);
            })

        );
    }

    public function getGlobals()
    {
        return array(
            'zoho' => $this->app['zoho']
        );
    }
}
