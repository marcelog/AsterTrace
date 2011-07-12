<?php
require_once 'Ding/Autoloader/Autoloader.php';
\Ding\Autoloader\Autoloader::register();

if ($argc != 2) {
    echo implode(' ', array(
        'Use:', $argv[0], '<config_dir>'
    ));
    exit(253);
}
$configDir = $argv[1];
$log4php = $configDir . DIRECTORY_SEPARATOR . 'log4php.properties';
$dingUserProperties = array('config.dir' => $configDir);
$dingDrivers = array(
    'errorhandler' => array(),
    'shutdown' => array(),
    'signalhandler' => array()
);
$dingBdef = array(
    'xml' => array(
        'filename' => 'beans.xml',
        'directories' => array($configDir . DIRECTORY_SEPARATOR . 'support')
    )
);

$dingProperties = array('ding' => array(
    'log4php.properties' => $log4php,
    'factory' => array(
        'properties' => $dingUserProperties,
        'drivers' => $dingDrivers,
        'bdef' => $dingBdef
    )
));

