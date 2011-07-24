<?php
/**
 * Listens for VarSet's and log them to database with
 * name and value, grouping by uniqueid.
 *
 * PHP Version 5
 *
 * @category AsterTrace
 * @package  EventHandlers
 * @author   Marcelo Gornstein <marcelog@gmail.com>
 * @license  http://marcelog.github.com/ Apache License 2.0
 * @version  SVN: $Id$
 * @link     http://marcelog.github.com/
 *
 * Copyright 2011 Marcelo Gornstein <marcelog@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */
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

