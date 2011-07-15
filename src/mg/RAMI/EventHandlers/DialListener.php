<?php
namespace EventHandlers;

class DialListener implements \Ding\Logger\ILoggerAware
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

    /**
     * @var \Logger
     */
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

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

    /**
     * Execute a pdo statement, binding the arguments.
     *
     * @param \PDOStatement $statement Statement to execute
     * @param array         $args      Arguments to bind
     *
     * @return void
     */
    private function _executeStatement(\PDOStatement $statement, array $args)
    {
        $result = $statement->execute($args);
        if ($result === false) {
            $this->logger->error(
                $statement->errorCode() . ': '
                . print_r($statement->errorInfo(), true)
            );
        }
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
            $this->_executeStatement($statement, $args);
        }
    }

    public function onHangup($event)
    {
        $this->_executeStatement($this->_hangupStatement, array(
            'uniqueidSrc' => $event->getUniqueId(),
            'cause' => $event->getCause(),
            'causeTxt' => $event->getCauseText()
        ));
    }

    public function onDialEnd($event)
    {
        $this->_executeStatement($this->_endCallStatement, array(
            'uniqueidSrc' => $event->getUniqueId(),
            'status' => $event->getDialStatus(),
            'eventEnd' => serialize($event)
        ));
    }

    public function onDialBegin($event)
    {
        $this->_executeStatement($this->_startCallStatement, array(
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
        $this->_executeStatement($this->_createStatement, array());
    }
}


