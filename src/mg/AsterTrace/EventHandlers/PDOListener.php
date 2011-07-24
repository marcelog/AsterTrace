<?php
/**
 * A superclass for all PDO-dependant listeners.
 *
 * PHP Version 5
 *
 * @category AsterTrace
 * @package  EventHandlers
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
namespace AsterTrace\EventHandlers;

abstract class PDOListener implements \Ding\Logger\ILoggerAware
{
    /**
     * @var \PDOStatement
     */
    protected $insertStatement;

    /**
     * @var \PDOStatement
     */
    protected $createStatement;

    /**
     * @var \Logger
     */
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute a pdo statement, binding the arguments.
     *
     * @param \PDOStatement $statement Statement to execute
     * @param array         $args      Arguments to bind
     *
     * @return void
     */
    protected function executeStatement(\PDOStatement $statement, array $args)
    {
        $result = $statement->execute($args);
        if ($result === false) {
            $this->logger->error(
                $statement->errorCode() . ': '
                . print_r($statement->errorInfo(), true)
            );
        }
    }
    /**
     * This will be called by the container. Will execute the create
     * stable statement.
     *
     * @return void
     */
    public function init()
    {
        $this->executeStatement($this->createStatement, array());
    }

    /**
     * Called by the container.
     *
     * @return void
     */
    public function setCreateStatement($statement)
    {
        $this->createStatement = $statement;
    }

    /**
     * Called by the container.
     *
     * @return void
     */
    public function setInsertStatement($statement)
    {
        $this->insertStatement = $statement;
    }
}


