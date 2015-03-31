<?php

namespace API\Tests;

use Silex\WebTestCase;

class APITestBase extends WebTestCase {

  protected function getBaseUrl() {
    return '/drupalci/api/1';
  }


  public function createApplication() {
    $app = include __DIR__ . '/../src/app.php';
    return $app;
  }

}
