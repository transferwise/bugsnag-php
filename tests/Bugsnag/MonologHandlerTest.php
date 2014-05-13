<?php

class MonologHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $logger;
    protected $client;

    protected function setUp()
    {
        ini_set('date.timezone', 'America/Los_Angeles');

        $this->logger = new Monolog\Logger('test');
        $this->client = $this->getMockBuilder('Bugsnag_Client')
                             ->setMethods(array('notifyError', 'notifyException'))
                             ->setConstructorArgs(array('example-api-key'))
                             ->getMock();

        $this->logger->pushHandler(new Bugsnag_MonologHandler($this->client));
    }

    // TODO: Test each severity mapping
    public function testLog()
    {
        $this->client->expects($this->once())
                     ->method('notifyError');

        $this->logger->error("Something broke");
    }
}
