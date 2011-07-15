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
        $this->container->eventDispatch(substr(strrchr($eventClass, '\\'), 1, -5), $event);
    }
}


