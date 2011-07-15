<?php
namespace Handlers;

class PamiHandler implements
    \Ding\Helpers\PAMI\IPamiEventHandler, \Ding\Logger\ILoggerAware,
    \Ding\Container\IContainerAware
{
    protected $logger;
    protected $container;
    
    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }
    public function setContainer(\Ding\Container\IContainer $container)
    {
        $this->container = $container;
    }
    public function handlePamiEvent(\PAMI\Message\Event\EventMessage $event)
    {
        $eventClass = get_class($event);
        if (strstr($eventClass, "Dial")) {
            if ($event->getSubEvent() == "Begin") {
                $this->container->eventDispatch('dialStart', $event);
            } else if ($event->getSubEvent() == "End") {
                $this->container->eventDispatch('dialEnd', $event);
            }
        }
    }
}


