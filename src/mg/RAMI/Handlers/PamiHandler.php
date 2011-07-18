<?php
namespace Handlers;

class PamiHandler implements
    \Ding\Helpers\PAMI\IPamiEventHandler, \Ding\Logger\ILoggerAware,
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

        // Get the class of the event, something like PAMI\Message\Event\SomeEvent.
        $eventClass = get_class($event);

        // Get to the last \ and copy from there.
        $eventName = substr($eventClass, strrpos($eventClass, '\\') + 1);

        // Strip "Event" from the end of the string and lowercase the first letter.
        $eventName = lcfirst(substr($eventName, 0, -5));

        // After all of this, the resulting event name will be "some".
        if (method_exists($event, 'getSubEvent')) {
            // If this event has a subevent string, then concatenate it to the
            // event name, like someSubEvent.
            $eventName .= $event->getSubEvent();
        }
        $this->logger->debug('Dispatching: ' . $eventName);
        $this->container->eventDispatch($eventName, $event);
    }
}


