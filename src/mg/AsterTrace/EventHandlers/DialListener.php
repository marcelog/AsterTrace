<?php
/**
 * Listenes on: Hangup, Dial (Begin), Dial (End), VarSet
 * for variables DIALEDTIME, and ANSWEREDTIME.
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

class DialListener extends PDOListener
{
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
        $this->executeStatement($this->insertStatement, array(
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
}


