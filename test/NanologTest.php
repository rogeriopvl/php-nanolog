<?php
require_once dirname(__FILE__).'/../src/Nanolog/Nanolog.php';

class NanologTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // TODO
    }

    /**
     * @expectedException Exception
     */
    public function testThrowsExceptionWhenFolderDoesNotExist()
    {
        $log = new \Nanolog\Nanolog(\Nanolog\Nanolog::DEBUG, '/xpto/foo');
    }

    /**
     * @expectedException Exception
     */
    public function testThrowsExceptionWhenFolderDoesHaveWritePermissions()
    {
        $log = new \Nanolog\Nanolog(\Nanolog\Nanolog::DEBUG, '/var');
    }

    public function testIsClosedOnStart()
    {
        $log = new \Nanolog\Nanolog(\Nanolog\Nanolog::DEBUG, '/tmp');
        $this->assertEquals(PHPUnit_Framework_Assert::readAttribute($log, '_status'), \Nanolog\Nanolog::CLOSED);
    }
}
