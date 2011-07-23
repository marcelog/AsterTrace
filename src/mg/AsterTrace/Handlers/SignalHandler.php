<?php
namespace AsterTrace\Handlers;

class SignalHandler implements
    \Ding\Helpers\SignalHandler\ISignalHandler, \Ding\Logger\ILoggerAware
{
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleSignal($signal)
    {
        global $running;
        $this->logger->info('Got signal: ' . $signal);
        $running = false;
    }
}


