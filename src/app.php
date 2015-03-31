<?php

$loader = require_once __DIR__.'/../vendor/autoload.php';
//$loader->add('', __DIR__);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DerAlex\Silex\YamlConfigServiceProvider;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\MonologServiceProvider;
use API\Jenkins;
use API\Results;

$app = new Silex\Application();

/**
 * Environment.
 */
if (file_exists('/etc/drupalci-results.yaml')) {
    $config = '/etc/drupalci-results.yaml';
}
else {
    $config = __DIR__ . '/../config/config-test.yaml';
}

/**
 * Services.
 */
$app->register(new YamlConfigServiceProvider($config));

/**
 * Jenkins is a service.
 */
$app['service'] = $app->share(
  function ($app) {
    $jenkins = new Jenkins();
    $jenkins->setHost($app['config']['jenkins']['host']);
    $jenkins->setPort($app['config']['jenkins']['port']);
    $jenkins->setToken($app['config']['jenkins']['token']);
    $jenkins->setBuild($app['config']['jenkins']['job']);
    return $jenkins;
  }
);

/**
 * Results is a service.
 */
$app['results'] = $app->share(
  function ($app) {
    $results = new Results();
    $results->setUrl($app['config']['results']['host']);
    $results->setAuth($app['config']['results']['username'], $app['config']['results']['password']);
    return $results;
  }
);

/**
 * Handling.
 */
$app->error(function (\Exception $e, $code) {
  if ($e instanceof HttpException) {
    return new Response($e->getMessage(), $e->getStatusCode());
  }
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

// Set up JSON as a middleware.
$app->before(function (Request $request) {
  if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
  }
});

// Make sure we wrap JSONP in a callback if present.
$app->after(function (Request $request, Response $response) {
  if ($response instanceof JsonResponse) {
    $callback = $request->get('callback', '');
    if ($callback) {
      $response->setCallback($callback);
    }
  }
});

// Security definition.
/**$app->register(new SecurityServiceProvider());
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
);*/

/**
 * Routing.
 */
$app['routes'] = $app->extend('routes', function (RouteCollection $routes, Application $app) {
    $loader = new YamlFileLoader(new FileLocator(__DIR__));
    $collection = $loader->load('routes.yml');
    $routes->addCollection($collection);
    return $routes;
});

return $app;
