<?php
namespace AsterTrace\EventHandlers;

class VarSetListener extends PDOListener
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

    public function onVarSet($event)
    {
        $this->executeStatement($this->_insertStatement, array(
            'value' => $event->getValue(),
            'uniqueid' => $event->getUniqueId(),
            'channel' => $event->getChannel(),
            'name' => $event->getVariableName()
        ));
    }

    public function init()
    {
        $this->executeStatement($this->_createStatement, array());
    }
}


