<?php

namespace API\Tests;

use Silex\WebTestCase;

class APITestBase extends WebTestCase {

  protected function getBaseUrl() {
    return '/drupalci/api/1';
  }

  public function createApplication() {
    $app = include __DIR__ . '/../src/app.php';

/**    $mock_guzzle = $this->getMockBuilder('\GuzzleHttp\Client')
      ->setMethods(['get'])
      ->disableOriginalConstructor()
      ->getMock();
    $mock_guzzle->expects($this->any())
      ->method('get')
      ->willReturnCallback(
          function ($url, $options) {
            return [$url, $options];
          }
        );

    $jenkins = $app['jenkins'];
    $jenkins->setClient($mock_guzzle);
*/
    return $app;
  }

}
