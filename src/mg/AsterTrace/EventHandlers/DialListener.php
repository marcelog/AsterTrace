<?php
namespace AsterTrace\EventHandlers;

class DialListener extends PDOListener
{
    /**
     * @var \PDOStatement
     */
    private $_startCallStatement;

    /**
     * @var \PDOStatement
     */
    private $_endCallStatement;

    /**
     * @var \PDOStatement
     */
    private $_hangupStatement;

    /**
     * @var \PDOStatement
     */
    private $_dialedTimeStatement;

    /**
     * @var \PDOStatement
     */
    private $_answeredTimeStatement;

    /**
     * @var \PDOStatement
     */
    private $_createStatement;

    public function setCreateStatement($statement)
    {
        $this->_createStatement = $statement;
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
            $this->executeStatement($statement, $args);
        }
    }

    public function onHangup($event)
    {
        $this->executeStatement($this->_hangupStatement, array(
            'uniqueidSrc' => $event->getUniqueId(),
            'cause' => $event->getCause(),
            'causeTxt' => $event->getCauseText()
        ));
    }

    public function onDialEnd($event)
    {
        $this->executeStatement($this->_endCallStatement, array(
            'uniqueidSrc' => $event->getUniqueId(),
            'status' => $event->getDialStatus(),
            'eventEnd' => serialize($event)
        ));
    }

    public function onDialBegin($event)
    {
        $this->executeStatement($this->_startCallStatement, array(
            'uniqueidSrc' => $event->getUniqueId(),
            'uniqueidDst' => $event->getDestUniqueID(),
            'eventStart' => serialize($event),
            'channelSrc' => $event->getChannel(),
            'channelDst' => $event->getDestination(),
            'dialString' => $event->getDialString(),
            'clidName' => $event->getCallerIDName(),
            'clidNum' => $event->getCallerIDNum()
        ));
    }

    public function init()
    {
        $this->executeStatement($this->_createStatement, array());
    }
}


