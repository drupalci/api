<?php

namespace API;

use Symfony\Component\HttpFoundation\Request;

class Job {

  public static function createFromRequest(Request $request) {
    return new static();
  }

}
