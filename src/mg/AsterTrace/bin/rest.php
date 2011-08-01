<?php
/**
 * Main entry point (as a rest service). Will bootstrap, instantiate the
 * container and start things up.
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
