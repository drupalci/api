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
use API\Mock\MockJenkins;
use API\Mock\MockResults;

$app = new Silex\Application();

/**
 * Environment.
 *
 * If we can pull the config from our known location, do so. Otherwise grab the
 * test config.
 */
if (file_exists('/etc/drupalci/config.yaml')) {
    $config = '/etc/drupalci/config.yaml';
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
$app['jenkins'] = $app->share(
  function ($app) {
    $conf = array();
    foreach (['host', 'port', 'job', 'token'] as $conf_key) {
      $conf[$conf_key] = !empty($app['config']['jenkins'][$conf_key]) ? $app['config']['jenkins'][$conf_key] : "";
    }

    // Sanity check.
    if (empty($conf['host']) || empty($conf['port']) || empty($conf['job'])) {
      // @todo: Make a meaningful exception class.
      throw new \InvalidArgumentException('Job start requests need at least a job title, repository and a branch.');
    }

    $jenkins = new Jenkins();
    $jenkins->setHost($conf['host']);
    $jenkins->setPort($conf['port']);
    $jenkins->setToken($conf['token']);
    $jenkins->setBuild($conf['job']);
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

// Set up JSON as a middleware.
$app->before(function (Request $request, Application $app) {
  if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
    $data = json_decode($request->getContent(), true);
    $request->request->replace(is_array($data) ? $data : array());
  }
});

// Set up the environment based on the request.
$app->before(function (Request $request, Application $app) {
  $env = $request->get('env', '');
  $app['env'] = $env;
  if ($env == 'mock') {
    $app['jenkins'] = $app->share(function() {return new MockJenkins();});
    $app['results'] = $app->share(function() {return new MockResults();});
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
$encoder = new MessageDigestPasswordEncoder();
$users = array();
foreach ($app['config']['users'] as $username => $password) {
  $users[$username] = array(
    'ROLE_USER',
    $encoder->encodePassword($password, ''),
  );
}
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

return $app;
