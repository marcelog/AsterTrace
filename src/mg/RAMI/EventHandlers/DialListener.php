<?php
namespace EventHandlers;

class DialListener implements \Ding\Logger\ILoggerAware
{
    private $_startCallStatement;
    private $_endCallStatement;
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setStartCallStatement($statement)
    {
        $this->_startCallStatement = $statement;
    }

    public function setEndCallStatement($statement)
    {
        $this->_endCallStatement = $statement;
    }

    public function onDialEnd($event)
    {
        $result = $this->_endCallStatement->execute(array(
            'uniqueid' => $event->getUniqueId(),
            'status' => $event->getDialStatus(),
            'eventEnd' => serialize($event)
        ));
        if ($result === false) {
            $this->logger->error(
                $this->_endCallStatement->errorCode() . ': '
                . print_r($this->_endCallStatement->errorInfo(), true)
            );
        }
    }

    public function onDialBegin($event)
    {
        $result = $this->_startCallStatement->execute(array(
            'uniqueid' => $event->getUniqueId(),
            'eventStart' => serialize($event),
            'channelSrc' => $event->getChannel(),
            'channelDst' => $event->getDestination(),
            'dialString' => $event->getDialString(),
            'clidName' => $event->getCallerIDName(),
            'clidNum' => $event->getCallerIDNum()
        ));
        if ($result === false) {
            $this->logger->error(
                $this->_startCallStatement->errorCode() . ': '
                . print_r($this->_startCallStatement->errorInfo(), true)
            );
        }
    }
}


