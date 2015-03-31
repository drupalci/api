<?php

/**
 * @file
 * A base test for all our API tests.
 */

namespace API\Tests;

use Silex\WebTestCase;

/**
 * Base test class for API tests.
 *
 * We do this so we only have one place to manage the path to the app file
 * in createApplication().
 */
class V1ControllerTest extends APITestBase {

  public function testJobGet() {

  }

}
