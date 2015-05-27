<?php

use Silex\Provider\UrlGeneratorServiceProvider;

// bootstrap
include_once('vendor/autoload.php');
include_once('cockpit/bootstrap.php');

$app = new Silex\Application();
$app['debug'] = cockpit()['app.config']['debug'];

// register services
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Myspace\Provider\TwigServiceProvider());

// bind routes

$app->get('/', function() use ($app) {

	return $app['twig']->render('index.html.twig');

})->bind('home');

$app->run();