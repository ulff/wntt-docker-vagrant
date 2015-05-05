<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CompanyController extends FOSRestController
{
    /**
     * Returns collection of Company objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Company objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getCompaniesAction()
    {
        $companies = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->findAll();

        $view = $this->view($companies, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Company object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Company object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getCompanyAction($id)
    {
        $product = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->find($id);

        if (!$product) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($product, 200);
        return $this->handleView($view);
    }
}