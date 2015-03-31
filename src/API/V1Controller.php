<?php

namespace API;

use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use DrupalCIResultsApi\API;

/**
 * Controller for Version 1 of the DrupalCI API.
 */

class V1Controller extends BaseController {

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
    $title = !empty($_GET['title']) ? $_GET['title'] : '';

    // Check params.
    if (empty($repository)) {
      return 'Please provide a repository.';
    }
    if (empty($branch)) {
      return 'Please provide a branch.';
    }
    if (empty($title)) {
      return 'Please provide a title.';
    }

    // Create a results site "stub" so the Jenkins slaves and send results
    // to it.
    $api = new API();
    $api->setUrl($app['config']['results']['host']);
    $api->setAuth($app['config']['results']['username'], $app['config']['results']['password']);
    $nid = $api->create($title);

    // Now send these details over to the Jenkins instance so the job can be
    // processed.
    $query = array(
      'repository' => $repository,
      'branch' => $branch,
      'patch' => $patch,
      'results' => $nid,
    );
    $jenkins = new Jenkins();
    $jenkins->setHost($app['config']['jenkins']['host']);
    $jenkins->setPort($app['config']['jenkins']['port']);
    $jenkins->setToken($app['config']['jenkins']['token']);
    $jenkins->setBuild($app['config']['jenkins']['job']);
    $jenkins->setQuery($query);
    $url = $jenkins->send();

    // Check the return to make sure we had a successful submission.
    if (empty($url)) {
      return new Response("The submission was not successful.");
    }

    return new Response($nid);
  }

  public function jobStatus(Application $app, $id) {
    $api = new API();
    $api->setUrl($app['config']['results']['host']);
    $api->setAuth($app['config']['results']['username'], $app['config']['results']['password']);

    echo $id;

    return $api->get($id);
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
