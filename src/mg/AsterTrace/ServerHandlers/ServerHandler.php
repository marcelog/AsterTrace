<?php
namespace AsterTrace\ServerHandlers;

abstract class ServerHandler implements
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

    protected function executeAction(\PAMI\Message\Action\ActionMessage $action, ServerCommandDTO $dto)
    {
        $response = $this->pami->send($action);
        $result = $response->getKeys();
        $result['events'] = array();
        foreach ($response->getEvents() as $eventResponse) {
            $result['events'][] = $eventResponse->getKeys();
        }
        $this->server->write($dto->address, $dto->port, json_encode($result) . "\r\n\r\n");
    }

    public function __construct(\Ding\Helpers\TCP\TCPServerHelper $tcpServer, \Ding\Helpers\PAMI\PamiHelper $pami)
    {
        $this->server = $tcpServer;
        $this->pami = $pami;
    }
}

