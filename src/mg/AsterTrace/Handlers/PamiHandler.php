<?php
/**
 * PAMI Handler. Gets called by the Ding pami helper for
 * any events sent by asterisk. This will dispatch the
 * events through the container.
 *
 * PHP Version 5
 *
 * @category AsterTrace
 * @package  Handlers
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
namespace AsterTrace\Handlers;

class PamiHandler implements
    \Ding\Helpers\Pami\IPamiEventHandler, \Ding\Logger\ILoggerAware,
    \Ding\Container\IContainerAware
{
    /**
     * @var \Logger
     */
    protected $logger;

    /**
     * @var \Ding\Container\IContainer
     */
    protected $container;
    
    /**
     * @var integer
     */
    private $_eventsNumber;

    /**
     * @var integer
     */
    private $_started;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function setContainer(\Ding\Container\IContainer $container)
    {
        $this->container = $container;
    }

    public function init()
    { 
        $this->_started = time();
        $this->_eventsNumber = 0;
    }

    public function shutdown()
    {
        $this->logger->info(
            'Handled: ' . intval($this->_eventsNumber)
            . ' events in ' . intval(time() - $this->_started)
            . ' seconds'
        );
    }

    public function handlePamiEvent(\PAMI\Message\Event\EventMessage $event)
    {
        $this->_eventsNumber++;

        // First, dispatch the event to all generic event listeners
        $this->container->eventDispatch('anyEvent', $event);

        // After all of this, the resulting event name will be "some".
        $eventName = lcfirst($event->getName());
        if (method_exists($event, 'getSubEvent')) {
            // If this event has a subevent string, then concatenate it to the
            // event name, like someSubEvent.
            $eventName .= $event->getSubEvent();
        }
        $this->logger->debug('Dispatching: ' . $eventName);
        $this->container->eventDispatch($eventName, $event);
    }
}


