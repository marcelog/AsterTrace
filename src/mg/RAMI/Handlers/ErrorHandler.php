<?php
namespace Handlers;

class ErrorHandler implements
    \Ding\Helpers\ErrorHandler\IErrorHandler, \Ding\Logger\ILoggerAware
{
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleError(\Ding\Helpers\ErrorHandler\ErrorInfo $error)
    {
        $this->logger->error(print_r($error, true));
    }
}


