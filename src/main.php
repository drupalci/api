<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add('', __DIR__);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\MonologServiceProvider;

$app = new Silex\Application();

/**
 * Environment.
 */
if (file_exists('/etc/drupalci-results.yaml')) {
    $config = '/etc/drupalci-results.yaml';
}
else {
    $config = '../config/config-test.yaml';
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
 * Build out our users and give them appropriate roles.
 */
$encoder = new MessageDigestPasswordEncoder();
$users = array();
foreach ($app['config']['users'] as $username => $password) {
    $users[$username] = array(
        'ROLE_USER',
        $encoder->encodePassword($password, ''),
    );
}

// Security definition.
$app->register(new SecurityServiceProvider());
$app['security.firewalls'] = array(
    // Login URL is open to everybody.
    'default' => array(
        'pattern' => '^.*$',
        'http' => true,
        'stateless' => true,
        'users' => $users,
    ),
);
$app['security.access_rules'] = array(
    array('^.*$', 'ROLE_USER'),
);

/**
 * Routing.
 */
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
    $loader = new YamlFileLoader(new FileLocator(__DIR__));
    $collection = $loader->load('routes.yml');
    $routes->addCollection($collection);
    return $routes;
});

$app->run();
