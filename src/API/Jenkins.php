<?php

namespace API;

use Silex\Exception as Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\Request;

/**
 * Class Jenkins
 * A generic build trigger class for Jenkins remote API calls.
 */
class Jenkins {

  /**
   * @var string
   */
  protected $host = '';

  /**
   * @var string
   */
  protected $port = '80';

  /**
   * @var string
   */
  protected $token = '';

  /**
   * @var string
   */
  protected $build = '';

  /**
   * @var array
   */
  protected $query = array();

  /**
   * @var \GuzzleHttp\Client
   */
  protected $client = NULL;

  /**
   *
   * @param Job $job
   */
  public function sendJob($job) {
    // Now send these details over to the Jenkins instance so the job can be
    // processed.
    $query = array(
      'repository' => $job->getRepository(),
      'branch' => $job->getBranch(),
      'patch' => $job->getPatch(),
      'results' => $job->getId(),
    );
    $this->setQuery($query);
    return $this->send();
  }

  /**
   * Helper function to build the URL of the Jenkins host.
   */
  protected function buildUrl() {
    $host = $this->getHost();
    $port = $this->getPort();
    $build = $this->getBuild();
    if (empty($host) || empty($port) || empty($build)) {
      throw new \InvalidArgumentException('Jenkins needs host, build, and port.');
    }

    // Guzzle doesn't like "example.com:80" as part of the url.
    if ($port == "80") {
      return $host . '/job/' . $build . '/buildWithParameters';
    }

    return $host . ':' . $port . '/job/' . $build . '/buildWithParameters';
  }

  /**
   * Helper function to build the request.
   */
  public function sendRequest() {
    // Add the Token to the query if it is set.
    $token = $this->getToken();
    $query = $this->getQuery();
    if ($token) {
      $query['token'] = $token;
    }

    // Post the request to Jenkins.
    $url = $this->buildUrl();
    $client = $this->getClient();

    try {
      $response = $client->get($url, [
        // @todo, Once we get signed certificates we should remove.
        'verify' => false,
        'query' => $query,
      ]);
    }
    catch (\Exception $e) {
      return NULL;
    }
    return $response;
  }

  /**
   * Send the data to the remote Jenkins host.
   */
  public function send() {
    $response = $this->sendRequest();
    if ($response) {
      $location = $response->getHeader('Location');
      // We get the location of the build in the queue so we can track it.
      // First we make sure it is in the right format.
      if (strpos($location, $this->buildUrl())) {
        return FALSE;
      }
      return $location;
    }
    return FALSE;
  }

  /**
   * @return string
   */
  public function getBuild() {
    return $this->build;
  }

  /**
   * @param string $build
   */
  public function setBuild($build) {
    $this->build = $build;
  }

  /**
   * @return \GuzzleHttp\Client
   */
  public function getClient() {
    if ($this->client === NULL) {
      $this->client = new GuzzleClient;
    }
    return $this->client;
  }

  /**
   * @param \GuzzleHttp\Client $client
   */
  public function setClient($client) {
    $this->client = $client;
  }

  /**
   * @return string
   */
  public function getHost() {
    return $this->host;
  }

  /**
   * @param string
   */
  public function setHost($host) {
    $this->host = $host;
  }

  /**
   * @return string
   */
  public function getPort() {
    return $this->port;
  }

  /**
   * @param string $port
   */
  public function setPort($port) {
    $this->port = $port;
  }

  /**
   * @return array
   */
  public function getQuery() {
    return $this->query;
  }

  /**
   * @param array $query
   */
  public function setQuery($query) {
    $token = $this->getToken();
    if ($token) {
      $query['token'] = $token;
    }
    $this->query = $query;
  }

  /**
   * @return string
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * @param string $token
   */
  public function setToken($token) {
    $this->token = $token;
  }

}
