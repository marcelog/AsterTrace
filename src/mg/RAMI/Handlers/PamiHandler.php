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
        $this->container->eventDispatch('anyEvent', $event);
        $eventClass = get_class($event);
        $eventName = substr($eventClass, strrpos($eventClass, '\\') + 1);
        $eventName = lcfirst(substr($eventName, 0, -5));
        if (method_exists($event, 'getSubEvent')) {
            $eventName .= $event->getSubEvent();
        }
        $this->logger->info('Dispatching: ' . $eventName);
        $this->container->eventDispatch($eventName, $event);
    }
}


