<?php
/**
 * This handler will execute a CoreShowChannels action.
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

class CoreShowChannels extends ServerHandler
{
    /**
     * Called by the container.
     *
     * @param unknown_type $event
     */
    public function onServerCoreShowChannels(ServerCommandDTO $event)
    {
        $action = new \PAMI\Message\Action\CoreShowChannelsAction;
        parent::executeAction($action, $event);
    }
}

