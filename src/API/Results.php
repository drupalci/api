<?php

namespace API;

use DrupalCIResultsApi\Api;

/**
 * Encapsulate interactions with the Results server.
 */
class Results {

  protected $api;

  /**
   * Construct a Results API object.
   */
  public function __construct() {
    $this->api = new Api();
  }

  /**
   * Get a record from the Results server.
   *
   * @param string $id
   */
  public function getStatus($id) {

  }

  /**
   *
   * @param string $url
   */
  public function setUrl($url) {
    $this->api->setUrl($url);
  }

  /**
   * @param array $auth
   */
  public function setAuth($username, $password) {
    $this->api->setAuth($username, $password);
  }

  /**
   * @param \API\Job $job
   */
  public function createResultForJob(Job $job) {
    try {
      $nid = $this->api->create($job->getTitle());
      $job->setId($nid);

      // This is always going to be marked as a new object.
      $job->setStatus("new");

      // This is the results report used for developers to see the build results.
      $url = $this->api->getUrl();
      $job->setResult($url . '/node/' . $nid);
    } catch (\Exception $e) {
      // @todo: make this give useful information.
    }

    return $job;
  }

}
