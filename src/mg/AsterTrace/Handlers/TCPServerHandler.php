<?php
/**
 * The TCP server. Will dispatch async events and trigger events through the
 * container for every client command.
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

class TcpServerHandler implements
    \Ding\Logger\ILoggerAware,
    \Ding\Container\IContainerAware,
    \Ding\Helpers\Tcp\ITcpServerHandler
{
    protected $clients = array();
    /**
     * @var \Logger
     */
    protected $logger;
    /**
     * @var \Ding\Container\IContainer
     */
    protected $container;

    /**
     * Called by the container.
     *
     * @param \Logger $logger
     *
     * @return void
     */
    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Called by the container.
     *
     * @param \Ding\Container\IContainer $container
     *
     * @return void
     */
    public function setContainer(\Ding\Container\IContainer $container)
    {
        $this->container = $container;
    }

    public function beforeOpen()
    {
    }

    public function beforeListen()
    {
    }

    public function close()
    {
        $this->logger->info('Server closed');
    }

    public function onAnyEvent($event)
    {
        foreach ($this->clients as $client => $peer)
        {
            $peer->write(json_encode($event->getKeys()) . "\r\n\r\n");
        }
    }

    public function handleConnection(\Ding\Helpers\Tcp\TcpPeer $peer)
    {
        $this->logger->info('New connection from: ' . $peer->getName());
        $this->clients[$peer->getName()] = $peer;
    }

    public function readTimeout(\Ding\Helpers\Tcp\TcpPeer $peer)
    {
        $this->logger->info('Timeout for: ' . $peer->getName());
        $peer->disconnect();
    }

    public function handleData(\Ding\Helpers\Tcp\TcpPeer $peer)
    {
        $buffer = '';
        $len = 4096;
        $peer->read($buffer, $len);
        $this->logger->info('Got from: ' . $peer->getName() . ': ' . $buffer);
        $data = new \AsterTrace\ServerHandlers\ServerCommandDTO;
        $data->peer = $peer;
        $data->data = $buffer;
        $cmd = explode(' ', $buffer);
        $this->container->eventDispatch('server' . $cmd[0], $data);
    }

    public function disconnect(\Ding\Helpers\Tcp\TcpPeer $peer)
    {
        $this->logger->info('Disconnected: ' . $peer->getName());
        unset($this->clients[$peer->getName()]);
    }
}

