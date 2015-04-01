<?php

/**
 * @file
 * A base test for all our API tests.
 */

namespace API\Tests;

use Silex\WebTestCase;
use API\Tests\APITestBase;

/**
 * Base test class for API tests.
 *
 * We do this so we only have one place to manage the path to the app file
 * in createApplication().
 */
class V1ControllerTest extends APITestBase {

  public function testHomeGet() {
    $client = $this->createClient();
    $crawler = $client->request('GET', '/');

    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }

  public function testJobPostNoContent() {
    $client = $this->createClient();
    $path = $this->getBaseUrl() . '/job';
    $crawler = $client->request('POST', $this->getBaseUrl() . '/job');

    $this->assertEquals(400, $client->getResponse()->getStatusCode());
  }

  public function testJobPostMock() {
    // Build.
    $this->app['env'] = 'mock';
    $client = $this->createClient();
    $crawler = $client->request(
      'POST', $this->getBaseUrl() . '/job',
      [],
      [],
      array('CONTENT_TYPE' => 'application/json'),
      '{"branch":"r","repository":"b", "patch":"p", "title":"some title"}'
    );
    $response = $client->getResponse();

    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }


  public function testJobPostNoBackend() {
    // Build.
    $this->app['env'] = 'prod';
    $client = $this->createClient();
    $crawler = $client->request(
      'POST', $this->getBaseUrl() . '/job',
      [],
      [],
      array('CONTENT_TYPE' => 'application/json'),
      '{"branch":"r","repository":"b", "patch":"p", "title":"some title"}'
    );
    $response = $client->getResponse();

    $this->assertEquals(502, $client->getResponse()->getStatusCode());
  }

  public function testGetJob404() {
    $client = $this->createClient();
    $crawler = $client->request('GET', $this->getBaseUrl() . '/job/0');

    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }

}
