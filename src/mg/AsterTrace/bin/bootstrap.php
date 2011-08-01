<?php
/**
 * Bootstrapping code. Set include paths, setup
 * container configuration.
 *
 * PHP Version 5
 *
 * @category AsterTrace
 * @package  Bin
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
ini_set('include_path', implode(PATH_SEPARATOR, array(
    ini_get('include_path'),
    realpath(implode(DIRECTORY_SEPARATOR, array(
        __DIR__, '..', '..'
    )))
)));
require_once 'Ding/Autoloader/Autoloader.php';
\Ding\Autoloader\Autoloader::register();

if (php_sapi_name() == 'cli') {
    if ($argc != 2) {
        echo implode(' ', array(
        	'Use:', $argv[0], '<config_dir>'
        ));
        exit(253);
    }
    $configDir = $argv[1];
    $beans = 'cli.xml';
    $log4php = 'log4php.properties';
} else {
    $configDir = getenv('CONFIG_DIR');
    $beans = 'rest.xml';
    $log4php = 'log4php-rest.properties';
}
$dingUserProperties = array('config.dir' => $configDir);
$dingDrivers = array(
    'errorhandler' => array(),
    'shutdown' => array(),
    'signalhandler' => array()
);
$dingBdef = array(
    'xml' => array(
        'filename' => $beans,
        'directories' => array($configDir . DIRECTORY_SEPARATOR . 'support')
    )
);

$dingProperties = array('ding' => array(
    'log4php.properties' => $configDir . DIRECTORY_SEPARATOR . $log4php,
    'factory' => array(
        'properties' => $dingUserProperties,
        'drivers' => $dingDrivers,
        'bdef' => $dingBdef
    )
));

