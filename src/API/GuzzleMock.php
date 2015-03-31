<?php

namespace API;

/**
 * Class GuzzleMock
 * A mock Guzzle class.
 */
class GuzzleMock {

  /**
   * @{inheritdoc}
   */
  public function get($url, $options) {
    return array($url, $options);
  }

}
