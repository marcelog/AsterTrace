<?php
namespace AsterTrace\Handlers;

class ShutdownHandler implements
    \Ding\Helpers\ShutdownHandler\IShutdownHandler, \Ding\Logger\ILoggerAware
{
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleShutdown()
    {
        $this->logger->info('Shutting down');
    }
}


