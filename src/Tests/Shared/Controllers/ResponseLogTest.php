<?php
namespace Nines\Tests\Shared\Controllers;

use Nines\Shared\Controllers\ResponseLog;
use Nines\Tests\TestSetup;
use PHPUnit\Framework\TestCase;

class ResponseLogTest extends TestCase
{
    private $responseLog;

    // Create an object to use:
    function setUp()
    {
        $testSetup = TestSetup::getInstance();
        $dbConn = $testSetup->getDbConn();
        $this->responseLog = new ResponseLog($dbConn);
    }

    public function testAddHeadResponse()
    {
        $responseArray = array(
            'urlId' => 99,
            'datetime' => '2018-12-25 00:00:02',
            'statusCode' => 200
        );
        $result = $this->responseLog->addHeadResponse($responseArray);
        $this->assertTrue($result);
    }

    public function testAddGetResponse()
    {
        $responseArray = array(
            'urlId' => 99,
            'datetime' => '2018-12-25 00:00:02',
            'statusCode' => 404,
            'errorMessage' => "Das ist kein error!",
            'responseBody' => "<html><head></head><body><h1>404 - Nicht Gefunden Baby!</h1></body></html>"
        );
        $result = $this->responseLog->addGetResponse($responseArray);
        $this->assertTrue($result);
    }
}
