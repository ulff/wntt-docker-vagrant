<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class StandController extends FOSRestController
{
    /**
     * Returns collection of Stand objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Stand objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getStandsAction()
    {
        $stands = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->findAll();

        $view = $this->view($stands, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Stand object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Stand object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getStandAction($id)
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($product, 200);
        return $this->handleView($view);
    }
}