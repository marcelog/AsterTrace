<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Main Entry Point
$retCode = 0;
$running = true;
try
{
    $container = \Ding\Container\Impl\ContainerImpl::getInstance(
        $dingProperties
    );
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
