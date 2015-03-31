<?php

namespace API;

use Silex\Application;

/**
 * @file
 * Defined interface for DrupalCI API.
 */

interface BaseInterface {

  /**
   * Information on how to use the API.
   * @return message.
   */
  public function home();

  /**
   * Runs a job.
   * @return id.
   */
  public function jobRun(Application $app);

  /**
   * Get the status of a job.
   * @return status.
   */
  public function jobStatus(Application $app, $id);

  /**
   * Cancel a job.
   * @return id.
   */
  public function jobCancel(Application $app, $id);

  /**
   * Restarts a job.
   * @return id.
   */
  public function jobRestart(Application $app, $id);

  /**
   * Gets a jobs console output from the dispatcher.
   * @return console output.
   */
  public function jobConsole(Application $app, $id);

  /**
   * Get the results of the build.
   * @return results.
   */
  public function jobResults(Application $app, $id);

  /**
   * Authenticate against the API.
   * @return success.
   */
  public function auth(Application $app, $token);

  /**
   * Get global API status.
   * @return status.
   */
  public function status(Application $app);

}
