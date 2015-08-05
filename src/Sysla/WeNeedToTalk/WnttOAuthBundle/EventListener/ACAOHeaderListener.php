<?php

namespace Sysla\WeNeedToTalk\WnttOAuthBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ACAOHeaderListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
    }
}