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
    // Get the request for convenience.
    $request = $app['request'];

    // Try to make a job object from the request. If there is a problem with
    // the POSTed data, Job will throw \InvalidArgumentException.
    try {
      $job = Job::createFromRequest($request);
    } catch (\InvalidArgumentException $e) {
      $app->abort(400, $e->getMessage());
    }

    // We have to create a record on the Results site so we have a job ID.
    $results = $app['results'];
    $job = $results->createResultForJob($job);

    $id = $job->getId();
    if (empty($id) || $id == 0) {
      $app->abort(502, 'Unable to submit to Results server.');
    }

    // Start the job under Jenkins.
    $jenkins = $app['jenkins'];
    $url = $jenkins->sendJob($job);

    // Check the return to make sure we had a successful submission.
    if (empty($url)) {
      $app->abort(502, 'Unable to submit to test builder.');
    }
    $job->setJenkinsUri($url);

    // Return the job object we made.
    return $app->json($job);
  }

  public function jobStatus(Application $app, $id) {
    // Get the results service.
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
