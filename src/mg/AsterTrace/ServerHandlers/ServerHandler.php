<?php
/**
 * The base class for all tcp server command handlers.
 *
 * PHP Version 5
 *
 * @category AsterTrace
 * @package  ServerHandlers
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
namespace AsterTrace\ServerHandlers;

abstract class ServerHandler implements
    \Ding\Container\IContainerAware, \Ding\Logger\ILoggerAware
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
     * @var \Ding\Helpers\PAMI\PamiHelper
     */
    protected $pami;

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

    /**
     * Call this one from your subclasses so the action is sent and the response
     * is serialized and sent to the given client.
     *
     * @param \PAMI\Message\Action\ActionMessage $action
     * @param ServerCommandDTO                   $dto
     *
     * @return void
     */
    protected function executeAction(\PAMI\Message\Action\ActionMessage $action, ServerCommandDTO $dto)
    {
        $response = $this->pami->send($action);
        $result = $response->getKeys();
        $result['events'] = array();
        foreach ($response->getEvents() as $eventResponse) {
            $result['events'][] = $eventResponse->getKeys();
        }
        $dto->peer->write(json_encode($result) . "\r\n\r\n");
    }

    /**
     * Constructor.
     *
     * @param \Ding\Helpers\PAMI\PamiHelper $pami
     *
     * @return void
     */
    public function __construct(\Ding\Helpers\PAMI\PamiHelper $pami)
    {
        $this->pami = $pami;
    }
}

