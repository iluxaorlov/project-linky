<?php

use App\Controller\RedirectController;
use Slim\App;
use Slim\Views\Twig;
use Slim\Http\Uri;
use Slim\Http\Environment;
use Slim\Views\TwigExtension;
use App\Handler\NotFoundHandler;
use App\Controller\IndexController;
use App\Controller\DirectionController;

require_once __DIR__ . '/../vendor/autoload.php';

session_name('LINKY');
session_start([
    'cookie_lifetime' => 365 * 24 * 60 * 60
]);

$settings = require_once __DIR__ . '/../app/Settings/App.php';

$app = new App([
    'settings' => $settings
]);

$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . '/../templates', [
        'cache' => false
    ]);

    $router = $container->get('router');
    $uri = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    return $view;
};

$container['notFoundHandler'] = function ($container) {
    return new NotFoundHandler($container['view']);
};

$app->get('/', IndexController::class . ':index')->setName('index');
$app->post('/', DirectionController::class . ':create')->setName('create');
$app->get('/{link}', RedirectController::class . ':redirect')->setName('redirect');

$app->run();