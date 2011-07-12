<?php

require_once 'Ding/Autoloader/Autoloader.php';

\Ding\Autoloader\Autoloader::register();
class MyPamiHandler implements
    \Ding\Helpers\PAMI\IPamiEventHandler, \Ding\Logger\ILoggerAware,
    \Ding\Container\IContainerAware
{
    private $_username;
    private $_password;
    private $_host;
    private $_port;
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
    public function setUsername($username)
    {
        $this->_username = $username;
    }
    public function setPassword($password)
    {
        $this->_password = $password;
    }
    public function setHost($host)
    {
        $this->_host = $host;
    }
    public function setPort($port)
    {
        $this->_port = $port;
    }
    
    public function handlePamiEvent(\PAMI\Message\Event\EventMessage $event)
    {
        $eventClass = get_class($event);
        $this->container->eventDispatch(substr(strrchr($eventClass, '\\'), 1, -5), $event);
    }
}

class MyCallListener
{
    private $_dbHost;
    private $_dbTable;
    private $_dbPort;
    private $_dbUsername;
    private $_dbPassword;
    private $_pdo;
    private $_dbStatement;

    public function setDBName($db)
    {
        $this->_dbName = $db;
    }
    public function setDBTable($table)
    {
        $this->_dbTable = $table;
    }
    public function setDBUsername($username)
    {
        $this->_dbUsername = $username;
    }
    public function setDBPassword($password)
    {
        $this->_dbPassword = $password;
    }
    public function setDBHost($host)
    {
        $this->_dbHost = $host;
    }
    public function setDBPort($port)
    {
        $this->_dbPort = $port;
    }
    public function init()
    {
        $string
            = 'mysql:dbname=' . $this->_dbName
            . ';host=' . $this->_dbHost
            . ';port=' . $this->_dbPort
        ;
        $this->_pdo = new PDO($string, $this->_dbUsername, $this->_dbPassword);
        $sql = 'INSERT INTO `' . $this->_dbTable . '` (`call`) VALUES(:call)';
        $this->_dbStatement = $this->_pdo->prepare($sql);
    }
    public function onDial($event)
    {
        $this->_dbStatement->execute(array('call' => serialize($event)));
    }
}

class MyShutdownHandler implements
    \Ding\Helpers\ShutdownHandler\IShutdownHandler, \Ding\Logger\ILoggerAware
{
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleShutdown()
    {
        $this->logger->info('Shutting down');
    }
}

class MyErrorHandler implements
    \Ding\Helpers\ErrorHandler\IErrorHandler, \Ding\Logger\ILoggerAware
{
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleError(\Ding\Helpers\ErrorHandler\ErrorInfo $error)
    {
        $this->logger->error(print_r($error, true));
    }
}

class MySignalHandler implements
    \Ding\Helpers\SignalHandler\ISignalHandler, \Ding\Logger\ILoggerAware
{
    protected $logger;

    public function setLogger(\Logger $logger)
    {
        $this->logger = $logger;
    }

    public function handleSignal($signal)
    {
        global $running;
        $this->logger->info('Got signal: ' . $signal);
        $running = false;
    }
}

// Main Entry Point
$retCode = 0;
$running = true;
try
{
    if ($argc != 2) {
        throw new \Exception(implode(' ', array(
            'Use:', $argv[0], '<config_dir>'
        )));
    }
    $configDir = $argv[1];
    $properties = array('ding' => array(
        'log4php.properties' => $configDir . DIRECTORY_SEPARATOR . 'log4php.properties',
        'factory' => array(
            'drivers' => array('errorhandler' => array(), 'shutdown' => array(), 'signalhandler' => array()),
            'properties' => array('config.dir' => $configDir),
            'bdef' => array(
                'xml' => array('filename' => 'beans.xml', 'directories' => array($configDir))
            )
        )
    ));
    $container = \Ding\Container\Impl\ContainerImpl::getInstance($properties);
    $listener = $container->getBean('pami');
    while($running) {
        $listener->process();
        usleep(1000);
    }
} catch(\Exception $exception) {
    echo get_class($exception) . ': ' . $exception->getMessage() . "\n";
    $retCode = 253;
}
exit($retCode);
