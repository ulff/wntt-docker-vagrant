<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class EventsController extends FOSRestController
{
    /**
     * Returns list of all events
     *
     * @ApiDoc(
     *  resource=true,
     *  description="short desc of getEventsAction",
     * )
     */
    public function getEventsAction()
    {
        $data = ['events' => []];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }

    /**
     * Returns event details
     *
     * @ApiDoc(
     *  resource=true,
     *  description="short desc of getEventAction(id)",
     * )
     */
    public function getEventAction($id)
    {
        $data = ['id' => $id];
        $view = $this->view($data, 200);
        return $this->handleView($view);
    }
}