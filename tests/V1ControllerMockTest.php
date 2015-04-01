<?php

/**
 * @file
 * A base test for all our API tests.
 */

namespace API\Tests;

use Silex\WebTestCase;
use API\Mock\MockJenkins;
use API\Mock\MockResults;

/**
 * Base test class for API tests.
 *
 * We do this so we only have one place to manage the path to the app file
 * in createApplication().
 */
class V1ControllerMockTest extends WebTestCase {

  public function createApplication() {
    $app = include __DIR__ . '/../src/app.php';

    $app['env'] = 'mock';

    $app['jenkins'] = $app->share(function() {return new MockJenkins();});
    $app['results'] = $app->share(function() {return new MockResults();});

    return $app;
  }

  public function testJobPostMock() {
    // Build.
    $this->app['env'] = 'mock';

    $client = $this->createClient(array(
      'PHP_AUTH_USER' => 'user1',
      'PHP_AUTH_PW'   => 'password1',
    ));
    $crawler = $client->request(
      'POST', 'drupalci/api/1/job',
      [],
      [],
      array('CONTENT_TYPE' => 'application/json'),
      '{"branch":"r","repository":"b", "patch":"p", "title":"some title"}'
    );
    $response = $client->getResponse();

    $this->assertEquals(200, $client->getResponse()->getStatusCode());
  }

}
