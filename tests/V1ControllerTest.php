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
    $crawler = $client->request('GET', $this->getBaseUrl());

    $this->assertEquals(404, $client->getResponse()->getStatusCode());
  }

  public function testJobPostNoContent() {
    $client = $this->createClient();
    $crawler = $client->request('POST', $this->getBaseUrl() . '/job');

    $this->assertEquals(401, $client->getResponse()->getStatusCode());
  }

  

}
