<?php
namespace AsterTrace\EventHandlers;

class EventListener extends PDOListener
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

    public function onAnyEvent($event)
    {
        $this->executeStatement($this->_insertStatement, array(
            'name' => $event->getName(),
            'event' => serialize($event)
        ));
    }

    public function init()
    {
        $this->executeStatement($this->_createStatement, array());
    }
}


