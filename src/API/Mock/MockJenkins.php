<?php

namespace API\Mock;

use API\Jenkins;

class MockJenkins extends Jenkins {

  public function sendJob($job) {
    return 'this is a mocked url';
  }

}
