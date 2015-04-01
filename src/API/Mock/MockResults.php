<?php

namespace API\Mock;

use API\Results;
use API\Job;

class MockResults extends Results {
  public function createResultForJob(Job $job) {
    $job->setId(1);
    return $job;
  }
}
