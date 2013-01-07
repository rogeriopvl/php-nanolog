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
        \Nanolog\Nanolog::create('/xpto/foo', \Nanolog\Nanolog::DEBUG);
    }

    /**
     * @expectedException Exception
     */
    public function testThrowsExceptionWhenFolderDoesHaveWritePermissions()
    {
        \Nanolog\Nanolog::create('/var', \Nanolog\Nanolog::DEBUG);
    }

    public function testCreateAddsAnInstance()
    {
        $log = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG);
        $this->assertInstanceOf('\Nanolog\Nanolog', $log);
    }

    /**
     * @depends testCreateAddsAnInstance
     */
    public function testCantCreateMoreThanOneAnonymousInstance()
    {
        $log = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG);
        $log2 = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG);

        $this->assertEquals(false, $log2);
    }

    /**
     * @depends testCreateAddsAnInstance
     */
    public function testCreateNamedInstance()
    {
        $log = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG, 'log1');

        $this->assertInstanceOf('\Nanolog\Nanolog', $log);
        $this->assertEquals('log1', $log->getName());
    }

    /**
     * @depends testCreateAddsAnInstance
     * @depends testCreateNamedInstance
     */
    public function testCreateMultipleNamedInstances()
    {
        $log1 = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG, 'log_1');
        $log2 = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG, 'log_2');

        $this->assertEquals('log_1', $log1->getName());
        $this->assertEquals('log_2', $log2->getName());
    }

    /**
     * @depends testCreateAddsAnInstance
     * @depends testCreateNamedInstance
     */
    public function testGetAnonymousInstance()
    {
        $log = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG);
        $logInstance = \Nanolog\Nanolog::getInstance();

        $this->assertInstanceOf('\Nanolog\Nanolog', $logInstance);
        $this->assertEquals(null, $logInstance->getName());
    }

    /**
     * @depends testCreateAddsAnInstance
     * @depends testCreateNamedInstance
     */
    public function testGetNamedInstance()
    {
        $log = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG, 'test1');
        $logInstance = \Nanolog\Nanolog::getInstance('test1');

        $this->assertInstanceOf('\Nanolog\Nanolog', $logInstance);
        $this->assertEquals('test1', $logInstance->getName());

    }

    public function testLogCritical()
    {
        $log = \Nanolog\Nanolog::create('/tmp', \Nanolog\Nanolog::DEBUG, 'test2');
        $log->critical('Disk is full');
    }

    /**
     * @depends testLogCritical
     */
    public function testLogError()
    {
        $log = \Nanolog\Nanolog::getInstance('test2');
        $log->error('Could not write message');

    }

    /**
     * @depends testLogCritical
     */
    public function testLogWarning()
    {
        $log = \Nanolog\Nanolog::getInstance('test2');
        $log->warning('Database is taking too long to respond');
    }

    /**
     * @depends testLogCritical
     */
    public function testLogInfo()
    {
        $log = \Nanolog\Nanolog::getInstance('test2');
        $log->info('User has logged out');
    }

    /**
     * @depends testLogCritical
     */
    public function testLogDebug()
    {
        $log = \Nanolog\Nanolog::getInstance('test2');
        $log->debug(print_r($log, true));
    }
}
