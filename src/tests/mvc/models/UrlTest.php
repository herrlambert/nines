<?php
namespace nines\tests\mvc\models;

use PHPUnit\Framework\TestCase;
use nines\mvc\models\Url;
use nines\tests\TestSetup;

class UrlTest extends TestCase
{
    private $testSetup;
    private $dbConn;
    private $url;

    // Create an object to use:
    function setUp()
    {
        $this->testSetup = TestSetup::getInstance();
        $this->dbConn = $this->testSetup->getDbConn();
        $this->url = new Url($this->dbConn);
    }

    public function testGetUrlGroups()
    {
        $groups = $this->url->getUrlGroups();
        $this->assertEquals(3, count($groups));
    }

    public function testGetUrlsByUrlGroupId()
    {
        $urls = $this->url->getUrlsByUrlGroupId(1);
        $this->assertEquals(3, count($urls));
    }
}
