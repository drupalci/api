<?php

namespace API\Tests;

use API\Job;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \API\Job
 */
class JobTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers ::createFromRequest
   */
  public function testCreateFromRequest() {
    $request = new Request(
      [
        'repository' => 'repository_test',
        'title' => 'title_test',
        'branch' => 'branch_test',
        'patch' => 'patch_test',
      ]
    );

    $job = Job::createFromRequest($request);
    $this->assertEquals('repository_test', $job->getRepository());
    $this->assertEquals('branch_test', $job->getBranch());
    $this->assertEquals('patch_test', $job->getPatch());
    $this->assertEquals('title_test', $job->getTitle());
  }

  /**
   * @covers ::createFromRequest
   * @dataProvider providerCreateFromRequestException
   * @expectedException \Exception
   */
  public function testCreateFromRequestException($request_data) {
    $request = new Request($request_data);
    $job = Job::createFromRequest($request);
  }

  public function providerCreateFromRequestException() {
    return [
      [['patch' => 'patch_test']],
      [['patch' => 'patch_test', 'branch' => 'branch_test']],
      [['patch' => 'patch_test', 'repository' => 'repository_test']],
    ];
  }

  /**
   * @covers ::jsonSerialize
   */
  public function testJsonSerialize() {
    $request = new Request(
      [
        'title' => 'title_test',
        'repository' => 'repository_test',
        'branch' => 'branch_test',
        'patch' => 'patch_test',
      ]
    );
    $job = Job::createFromRequest($request);
    $this->assertEquals(
      '{"id":0,"title":"title_test","jobType":null,"status":null,"result":null,"repository":"repository_test","branch":"branch_test","commit":null,"issue":null,"patch":"patch_test","tags":null,"environment":null}',
      json_encode($job->jsonSerialize())
    );
  }

}
