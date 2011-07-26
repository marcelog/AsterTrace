<?php
/**
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

class TCPServerHandler implements
    \Ding\Logger\ILoggerAware,
    \Ding\Container\IContainerAware,
    \Ding\Helpers\TCP\ITCPServerHandler
{
    /**
     * @var \Logger
     */
    protected $logger;
    protected $server;
    private $_pami;
    protected $clients = array();
    protected $container;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }
    public function setServer(\Ding\Helpers\TCP\TCPServerHelper $server)
    {
        $this->server = $server;
    }

    public function beforeOpen()
    {
    }

    public function beforeListen()
    {
    }

    public function close()
    {
    }

    public function setContainer(\Ding\Container\IContainer $container)
    {
        $this->container = $container;
    }

    public function onAnyEvent($event)
    {
        foreach ($this->clients as $client => $data)
        {
            $this->server->write($data[0], $data[1], serialize($event->getKeys()) . "\r\n\r\n");
        }
    }

    public function handleConnection($remoteAddress, $remotePort)
    {
        $this->clients[$remoteAddress . ':' . $remotePort] = array($remoteAddress, $remotePort);
    }

    public function readTimeout($remoteAddress, $remotePort)
    {
        $this->logger->info('Timeout for: ' . $remoteAddress . ':' . $remotePort);
        $this->server->disconnect($remoteAddress, $remotePort);
    }

    public function handleData($remoteAddress, $remotePort)
    {
        $buffer = '';
        $len = 4096;
        $this->server->read($remoteAddress, $remotePort, $buffer, $len);
        $this->logger->info('Got from: ' . $remoteAddress . ':' . $remotePort . ': ' . $buffer);
        $response = $this->_pami->send(new \PAMI\Message\Action\CoreShowChannelsAction);
        $this->server->write($remoteAddress, $remotePort, serialize($response));
        $this->container->eventDispatch('serverCommand', $buffer);
    }

    public function disconnect($remoteAddress, $remotePort)
    {
        unset($this->clients[$remoteAddress . ':' . $remotePort]);
    }
    public function __construct(\Ding\Helpers\PAMI\PamiHelper $pami)
    {
        $this->_pami = $pami;
    }
}

