<?php

namespace API;

/**
 * Encapsulate interactions with the Results server.
 */
class Results {

  /**
   * Gets a new job from the Results server.
   */
  public function createNewJob() {

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

  }

  /**
   * @param array $auth
   */
  public function setAuth($auth) {

  }

  /**
   * @param \API\Job $job
   */
  public function createResultForJob(Job $job) {
    return $job;
  }

}