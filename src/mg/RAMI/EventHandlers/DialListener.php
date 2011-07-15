<?php
namespace EventHandlers;

class DialListener implements \Ding\Logger\ILoggerAware
{
    private $_startCallStatement;
    private $_endCallStatement;
    private $_hangupStatement;
    private $_dialedTimeStatement;
    private $_answeredTimeStatement;
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setHangupStatement($statement)
    {
        $this->_hangupStatement = $statement;
    }

    public function setDialedTimeStatement($statement)
    {
        $this->_dialedTimeStatement = $statement;
    }

    public function setAnsweredTimeStatement($statement)
    {
        $this->_answeredTimeStatement = $statement;
    }

    public function setStartCallStatement($statement)
    {
        $this->_startCallStatement = $statement;
    }

    public function setEndCallStatement($statement)
    {
        $this->_endCallStatement = $statement;
    }

    public function onVarSet($event)
    {
        $statement = false;
        $args = array();
        $variable = $event->getVariableName();
        if ($variable === 'DIALEDTIME') {
            $statement = $this->_dialedTimeStatement;
            $args['uniqueidSrc'] = $event->getUniqueId();
            $args['timeDial'] = $event->getValue();
        } else if ($variable === 'ANSWEREDTIME') {
            $statement = $this->_answeredTimeStatement;
            $args['uniqueidSrc'] = $event->getUniqueId();
            $args['timeAnswer'] = $event->getValue();
        } 
        if ($statement !== false) {
            $result = $statement->execute($args);
            if ($result === false) {
                $this->logger->error(
                    $statement->errorCode() . ': '
                    . print_r($statement->errorInfo(), true)
                );
            }
        }
    }

    public function onHangup($event)
    {
        $result = $this->_hangupStatement->execute(array(
            'uniqueidSrc' => $event->getUniqueId(),
            'cause' => $event->getCause(),
            'causeTxt' => $event->getCauseText()
        ));
        if ($result === false) {
            $this->logger->error(
                $this->_hangupStatement->errorCode() . ': '
                . print_r($this->_hangupStatement->errorInfo(), true)
            );
        }
    }

    public function onDialEnd($event)
    {
        $result = $this->_endCallStatement->execute(array(
            'uniqueidSrc' => $event->getUniqueId(),
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
            'uniqueidSrc' => $event->getUniqueId(),
            'uniqueidDst' => $event->getDestUniqueID(),
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


