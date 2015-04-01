<?php

namespace API\Mock;

use API\Jenkins;

class MockJenkins extends Jenkins {

  public function sendRequest() {
    return ['', []];
  }

}
