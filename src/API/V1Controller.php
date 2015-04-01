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
      $app->abort(400, $e->getMessage());
    }

    // Create a results site "stub" so the Jenkins slaves can send results
    // post results to it.
    $results = $app['results'];
    $job = $results->createResultForJob($job);

    $id = $job->getId();
    if (empty($id) || $id == 0) {
      $app->abort(502, 'Unable to submit to Results server.');
    }

    $jenkins = $app['jenkins'];
    $url = $jenkins->sendJob($job);

    // Check the return to make sure we had a successful submission.
    if (empty($url)) {
      $app->abort(502, 'Unable to submit to test builder.');
    }
    $job->setJenkinsUri($url);

    return $app->json($job);
  }

  public function jobStatus(Application $app, $id) {
    $results = $app['results'];

    // @todo: Replace this with try block.
    $job = $results->getStatus($id);
    if (!$job) {
      $app->abort(404, 'Unable to find job.');
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
