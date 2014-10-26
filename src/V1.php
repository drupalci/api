<?php

namespace API;

use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Controller for Version 1 of the DrupalCI API.
 */

class V1 extends APIController implements APIInterface {

  /**
   * Information on how to use the API.
   * @return message.
   */
  public function home() {
    return new Response("Welcome to the DrupalCI API.");
  }

  /**
   * Runs a job.
   * @return id.
   */
  public function jobRun(Application $app) {
    // Get our params.
    // @todo, Find a better way to do this.
    $repository = !empty($_GET['repository']) ? $_GET['repository'] : '';
    $branch = !empty($_GET['branch']) ? $_GET['branch'] : '';
    $patch = !empty($_GET['patch']) ? $_GET['patch'] : '';

    // Check params.
    if (empty($repository)) {
      return 'Please provide a repository.';
    }
    if (empty($branch)) {
      return 'Please provide a branch.';
    }

    // Set for sending.
    $query = array(
      'repository' => $repository,
      'branch' => $branch,
      'patch' => $patch,
    );

    // Let the request begin.
    $jenkins = new Jenkins();
    $jenkins->setHost($app['config']['jenkins']['host']);
    $jenkins->setToken($app['config']['jenkins']['token']);
    $jenkins->setBuild($app['config']['jenkins']['job']);
    $jenkins->setQuery($query);
    $return = $jenkins->send();

    // Check the return to make sure we had a successful submission.
    if (empty($return)) {
      return new Response("The submission was not successful.");
    }

    return new Response('The build is in the queue at the following address: ' . $return);
  }

  /**
   * Authenticate against the API.
   * @return success.
   */
  public function auth(Application $app, $token) {
    // http://silex.sensiolabs.org/doc/providers/security.html
    return new Response("Not supported.");
  }

}
