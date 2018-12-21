<?php
namespace Nines\Tests\Shared\Controllers;

use Nines\Shared\Controllers\Url;
use PHPUnit\Framework\TestCase;
use Nines\Tests\TestSetup;

class UrlTest extends TestCase
{
    private $url;

    // Create an object to use:
    function setUp()
    {
        $testSetup = TestSetup::getInstance();
        $dbConn = $testSetup->getDbConn();
        $this->url = new Url($dbConn);
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
