<?php
namespace EventHandlers;

class DtmfListener extends PDOListener
{
    /**
     * @var \PDOStatement
     */
    private $_insertStatement;

    /**
     * @var \PDOStatement
     */
    private $_createStatement;

    public function setCreateStatement($statement)
    {
        $this->_createStatement = $statement;
    }

    public function setInsertStatement($statement)
    {
        $this->_insertStatement = $statement;
    }

    public function onDTMF($event)
    {
        if ($event->getEnd() == 'Yes') {
            $this->executeStatement($this->_insertStatement, array(
                'uniqueid' => $event->getUniqueId(),
                'dtmf' => $event->getDigit(),
                'channel' => $event->getChannel()
            ));
        }
    }

    public function init()
    {
        $this->executeStatement($this->_createStatement, array());
    }
}


