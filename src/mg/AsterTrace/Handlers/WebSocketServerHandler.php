<?php
/**
 * The Wbesocket server. Will dispatch async events and trigger events through the
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

class WebSocketServerHandler implements
    \Ding\Logger\ILoggerAware,
    \Ding\Container\IContainerAware,
    \Ding\Helpers\TCP\ITCPServerHandler
{
    protected $clients = array('saluted' => array(), 'unsaluted' => array());
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
        $this->clients['saluted'] = array();
        $this->clients['unsaluted'] = array();
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
        foreach ($this->clients['saluted'] as $client => $peer)
        {
           $peer->write(pack('c', (int)0) . utf8_encode(json_encode($event->getKeys())) . pack('c', (int)255));
        }
    }

    public function handleConnection(\Ding\Helpers\TCP\TCPPeer $peer)
    {
        $this->logger->info('New connection from: ' . $peer->getName());
        $this->clients['unsaluted'][$peer->getName()] = $peer;
    }

    public function readTimeout(\Ding\Helpers\TCP\TCPPeer $peer)
    {
        $this->logger->info('Timeout for: ' . $peer->getName());
        $peer->disconnect();
    }

    private function _getWebSocketKeyHash($key)
    {
        $keyLen = strlen($key);
        $digits = '';
        $spaces = 0;
        for ($i = 0; $i < $keyLen; $i++) {
            if (is_numeric($key[$i])) {
                $digits .= $key[$i];
            } else if ($key[$i] == ' ') {
                $spaces++;
            }
        }
        $div = (int)$digits / (int)$spaces;
        $this->logger->info('key |' . $key . '|: ' . $digits . ' / ' . $spaces . ' = ' . $div);
        return (int)$div;
    }

    private function _getWebSocketHeader($buffer, &$lines, &$keys)
    {
        $lines = explode("\r\n", $buffer);
        array_shift($lines);
        $keys = array();
        foreach ($lines as $line) {
            if (strlen($line) <= 2) {
                break;
            }
            $token = strpos($line, ':');
            $key = substr($line, 0, $token);
            $keys[$key] = substr($line, $token + 1);
        }
     }

    private function _handshake($peer)
    {
        $buffer = '';
        $len = 4096;
        $peerName = $peer->getName();
        $peer->read($buffer, $len);
        $this->logger->info('Got from: ' . $peerName . ': ' . $buffer);
        $this->_getWebSocketHeader($buffer, $lines, $keys);
        if (!isset($keys['Sec-WebSocket-Key1']) || !isset($keys['Sec-WebSocket-Key2'])) {
            $this->logger->error('Invalid websocket handshake for: ' . $peer->getName());
            $peer->disconnect();
        }
        $key1 = $this->_getWebSocketKeyHash(substr($keys['Sec-WebSocket-Key1'], 1));
        $key2 = $this->_getWebSocketKeyHash(substr($keys['Sec-WebSocket-Key2'], 1));
        $code = array_pop($lines);
        $key = pack('N', $key1) . pack('N', $key2) . $code;
        $this->logger->debug('1:|' . $key1 . '|- 2:|' . $key2 . '|3:|' . $code . '|4: ' . $key);
        $response = "HTTP/1.1 101 WebSocket Protocol Handshake\r\n";
        $response .= "Upgrade: WebSocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= "Sec-WebSocket-Origin: " . trim($keys['Origin']) . "\r\n";
        $response .= "Sec-WebSocket-Location: ws://" . trim($keys['Host']) . "/\r\n";
        $response .= "\r\n" . md5($key, true);
        $this->logger->debug($response);
        $peer->write($response);
        $this->clients['saluted'][$peerName] = $peer;
        unset($this->clients['unsaluted'][$peerName]);
    }

    public function handleData(\Ding\Helpers\TCP\TCPPeer $peer)
    {
        $peerName = $peer->getName();
        if (isset($this->clients['unsaluted'][$peerName])) {
           $this->_handshake($peer);
        } else {
            $buffer = '';
            $len = 4096;
            $peer->read($buffer, $len);
            $this->logger->info('Normal message: ' . $buffer);
        }
    }

    public function disconnect(\Ding\Helpers\TCP\TCPPeer $peer)
    {
        $this->logger->info('Disconnected: ' . $peer->getName());
        unset($this->clients[$peer->getName()]);
    }
}

