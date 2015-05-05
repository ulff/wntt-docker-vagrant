<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PresentationController extends FOSRestController
{
    /**
     * Returns collection of Presentation objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Presentation objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getPresentationsAction()
    {
        $presentations = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->findAll();

        $view = $this->view($presentations, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Presentation object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Presentation object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getPresentationAction($id)
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($product, 200);
        return $this->handleView($view);
    }
}