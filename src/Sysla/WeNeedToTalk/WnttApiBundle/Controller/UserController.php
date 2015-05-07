<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UserController extends FOSRestController
{
    /**
     * Returns collection of User objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of User objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getUsersAction()
    {
        $users = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->findAll();

        $view = $this->view($users, 200);
        return $this->handleView($view);
    }

    /**
     * Returns User object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns User object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getUserAction($id)
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($product, 200);
        return $this->handleView($view);
    }
}