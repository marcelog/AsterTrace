<?php
namespace EventHandlers;

abstract class PDOListener implements \Ding\Logger\ILoggerAware
{
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
}


