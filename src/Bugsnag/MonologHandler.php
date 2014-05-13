<?php

use \Monolog\Handler\AbstractProcessingHandler;
use \Monolog\Logger;

class Bugsnag_MonologHandler extends AbstractProcessingHandler
{
    private static $SEVERITY_MAPPING = array(
        Logger::DEBUG     => 'info',
        Logger::INFO      => 'info',
        Logger::NOTICE    => 'info',
        Logger::WARNING   => 'warning',
        Logger::ERROR     => 'error',
        Logger::CRITICAL  => 'error',
        Logger::ALERT     => 'error',
        Logger::EMERGENCY => 'error'
    );

    protected $client;

    public function __construct(Bugsnag_Client $client, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $severity = $this->getSeverity($record['level']);
        if (isset($record['context']['exception'])) {
            $this->client->notifyException(
                $record['context']['exception'],
                $record,
                $severity
            );
        } else {
            $this->client->notifyError(
                $severity,
                (string) $record['message'],
                $record,
                $severity
            );
        }
    }

    /**
     * Returns the Bugsnag severiry from a monolog error code.
     * @param int $errorCode - one of the Logger:: constants.
     * @return string
     */
    protected function getSeverity($errorCode)
    {
        if (isset(Bugsnag_MonologHandler::$SEVERITY_MAPPING[$errorCode])) {
            return Bugsnag_MonologHandler::$SEVERITY_MAPPING[$errorCode];
        } else {
            return Bugsnag_MonologHandler::$SEVERITY_MAPPING[Logger::ERROR];
        }
    }
}
