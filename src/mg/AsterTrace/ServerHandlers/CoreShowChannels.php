<?php
namespace AsterTrace\ServerHandlers;

class CoreShowChannels implements
    \Ding\Container\IContainerAware, \Ding\Logger\ILoggerAware
{
    protected $server;
    protected $logger;
    protected $container;
    protected $pami;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }
    public function setContainer(\Ding\Container\IContainer $container)
    {
        $this->container = $container;
    }

    public function onServerCoreShowChannels($event)
    {
        $action = new \PAMI\Message\Action\CoreShowChannelsAction;
        $response = $this->pami->send($action);
        $result = $response->getKeys();
        $result['events'] = array();
        foreach ($response->getEvents() as $eventResponse) {
            $result['events'][] = $eventResponse->getKeys();
        }
        $this->server->write($event->address, $event->port, json_encode($result) . "\r\n\r\n");
    }

    public function __construct(\Ding\Helpers\TCP\TCPServerHelper $tcpServer, \Ding\Helpers\PAMI\PamiHelper $pami)
    {
        $this->server = $tcpServer;
        $this->pami = $pami;
    }
}

