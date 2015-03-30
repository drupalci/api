<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use DerAlex\Silex\YamlConfigServiceProvider;
use Kir\Stores\KeyValueStores\Sqlite\PdoSqliteContextRepository;
use Silex\Application;

$app = new Silex\Application();

/**
 * Environment.
 */
if (file_exists('config.yaml')) {
  $config = 'config.yaml';
}
else {
  $config = '../tests/config.yaml';
}

/**
 * Services.
 */
$app->register(new YamlConfigServiceProvider($config));

/**
 * Handling.
 */
$app->error(function (\Exception $e, $code) {
  error_log($e);
  return "Something went wrong. Please contact the DrupalCI team.";
});

/**
 * Routing.
 */
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
  $loader = new YamlFileLoader(new FileLocator(__DIR__ . '/../config'));
  $collection = $loader->load('routes.yml');
  $routes->addCollection($collection);
  return $routes;
});

$app->run();
