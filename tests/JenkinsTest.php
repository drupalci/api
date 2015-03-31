<?php

namespace API;

use API\GuzzleMock;

/**
 * Class JenkinsTest.
 * Test the functionality of the Jenkins class.
 */
class JenkinsTest extends \PHPUnit_Framework_TestCase {

  /**
   * Build a Jenkins object and get the URL that will be used for submission.
   */
  public function testBuildUrl() {
    // Build.
    $guzzle = new GuzzleMock();
    $jenkins = new Jenkins($guzzle);
    $jenkins->setClient($guzzle);
    $jenkins->setProtocol('https');
    $jenkins->setHost('localhost');
    $jenkins->setPort('9090');
    $jenkins->setToken('99999999');
    $jenkins->setQuery(array(
      'repository' => 'baz',
      'branch' => 'bar',
      'patch' => 'bas'
    ));
    $jenkins->setBuild('foo');
    $request = $jenkins->sendRequest();

    // Check a successful request.
    $required = array('https://localhost:9090/job/foo/buildWithParameters', array(
      'query' => array(
        'token' => '99999999',
        'repository' => 'baz',
        'branch' => 'bar',
        'patch' => 'bas',
      ),
      'verify' => FALSE,
    ));
    $this->assertEquals($required, $request);

    // Check a successful return message.
    // @todo make this a separate test.
//    $message = $jenkins->send();
//    $this->assertEquals('The message has been sent to the dispatcher.', $message);
  }

}
