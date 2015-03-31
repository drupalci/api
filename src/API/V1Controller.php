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
    $request = $app['request'];

    try {
      $job = Job::createFromRequest($request);
    } catch (\InvalidArgumentException $e) {
      $app->abort(400, 'Job needs repository and branch.');
    }

    // Create a results site "stub" so the Jenkins slaves and send results
    // to it.
    $results = new Results();
    $results->setUrl($app['config']['results']['host']);
    $results->setAuth($app['config']['results']['username'], $app['config']['results']['password']);
    $job = $results->createResultForJob($job);

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
      $app->abort(500, 'Unable to submit to test builder.');
    }

    return new Response($nid);
  }

  public function jobStatus(Application $app, $id) {
    $results = new Results();
    $results->setUrl($app['config']['results']['host']);
    $results->setAuth($app['config']['results']['username'], $app['config']['results']['password']);

    $job =  $results->getJob($id);
    if (!$job) {
      $app->abort(404, 'Unable to find job.');
      return;
    }
    $app->json($job, 200);
  }

  /**
   * Authenticate against the API.
   * @return success.
   */
  public function auth(Application $app, $token) {
    // http://silex.sensiolabs.org/doc/providers/security.html
    $app->abort(500, 'Not supported.');
  }

}
