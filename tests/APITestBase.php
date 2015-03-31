<?php

namespace API\Tests;

use Silex\WebTestCase;

class APITestBase extends WebTestCase {

  public function createApplication() {
    $app = include __DIR__ . '/../src/app.php';
    return $app;
  }

}
