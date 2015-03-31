<?php

namespace API;

/**
 * Class JenkinsTest.
 * Test the functionality of the Jenkins class.
 */
class JenkinsTest extends \PHPUnit_Framework_TestCase {

  /**
   * @expectedException \InvalidArgumentException
   */
  public function testBuildUrlException() {
    $jenkins = new Jenkins();
    $ref_build_url = new \ReflectionMethod($jenkins, 'buildUrl');
    $ref_build_url->setAccessible(TRUE);
    $url = $ref_build_url->invoke($jenkins);
  }

  public function testBuildUrl() {
    $jenkins = new Jenkins();
    $jenkins->setProtocol('https');
    $jenkins->setHost('localhost');
    $jenkins->setPort('9090');
    $jenkins->setBuild('build');
    $ref_build_url = new \ReflectionMethod($jenkins, 'buildUrl');
    $ref_build_url->setAccessible(TRUE);
    $url = $ref_build_url->invoke($jenkins);
    $this->assertEquals('https://localhost:9090/job/build/buildWithParameters', $url);
  }

  /**
   * Build a Jenkins object and get the URL that will be used for submission.
   */
  public function testSendRequest() {
    // Build.
    $mock_guzzle = $this->getMockBuilder('\GuzzleHttp\Client')
      ->setMethods(['get'])
      ->disableOriginalConstructor()
      ->getMock();
    $mock_guzzle->expects($this->any())
      ->method('get')
      ->willReturnCallback(
          function ($url, $options) {
            return [$url, $options];
          }
        );

    $jenkins = new Jenkins();
    $jenkins->setClient($mock_guzzle);
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
    // @todo: turn this into a new test.
//    $message = $jenkins->send();
//    $this->assertEquals('The message has been sent to the dispatcher.', $message);
  }

}
