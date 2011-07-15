<?php
namespace EventHandlers;

class DialListener implements \Ding\Logger\ILoggerAware
{
    private $_insertStatement;
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setInsertStatement($statement)
    {
        $this->_insertStatement = $statement;
    }

    public function onDial($event)
    {
        $result = $this->_insertStatement->execute(array('call' => serialize($event)));
        if ($result === false) {
            $this->logger->error(
                $this->_insertStatement->errorCode() . ': '
                . print_r($this->_insertStatement->errorInfo(), true)
            );
        }
    }
}


