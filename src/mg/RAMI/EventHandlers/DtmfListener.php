<?php
namespace EventHandlers;

class DtmfListener implements \Ding\Logger\ILoggerAware
{
    /**
     * @var \PDOStatement
     */
    private $_insertStatement;

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

    public function setInsertStatement($statement)
    {
        $this->_insertStatement = $statement;
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

    public function onDTMF($event)
    {
        if ($event->getEnd() == 'Yes') {
            $this->_executeStatement($this->_insertStatement, array(
                'uniqueid' => $event->getUniqueId(),
                'dtmf' => $event->getDigit(),
                'channel' => $event->getChannel()
            ));
        }
    }

    public function init()
    {
        $this->_executeStatement($this->_createStatement, array());
    }
}


