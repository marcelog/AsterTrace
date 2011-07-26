<?php
namespace AsterTrace\ServerHandlers;

class CoreShowChannels extends ServerHandler
{
    public function onServerCoreShowChannels($event)
    {
        $action = new \PAMI\Message\Action\CoreShowChannelsAction;
        parent::executeAction($action, $event);
    }
}

