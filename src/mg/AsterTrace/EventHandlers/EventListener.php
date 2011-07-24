<?php
/**
 * Listens for all events. Will log all events to the
 * database by serializing the PAMI\Event\EventMessage class.
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

class EventListener extends PDOListener
{
    public function onAnyEvent($event)
    {
        $this->executeStatement($this->insertStatement, array(
            'name' => $event->getName(),
            'channel' => method_exists($event, 'getChannel') ? $event->getChannel() : '',
            'uniqueId' => method_exists($event, 'getUniqueId') ? $event->getUniqueId() : '',

            'privilege' => method_exists($event, 'getPrivilege') ? $event->getPrivilege() : '',
            'event' => serialize($event)
        ));
    }

}


