<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class EventController extends FOSRestController
{
    /**
     * Returns collection of Event objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Event objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getEventsAction()
    {
        $events = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->findAll();

        $view = $this->view($events, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Event object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Event object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getEventAction($id)
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($product, 200);
        return $this->handleView($view);
    }
}