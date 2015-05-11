<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\CompanyManager;

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
        $company = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->find($id);

        if (!$company) {
            throw $this->createNotFoundException('No company found for id '.$id);
        }

        $view = $this->view($company, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Creates Company object",
     *   parameters={
     *      {"name"="name", "dataType"="string", "description"="company name", "required"=true},
     *      {"name"="websiteUrl", "dataType"="string", "description"="company's website URL", "required"=false},
     *      {"name"="logoUrl", "dataType"="string", "description"="company's logo URL", "required"=false}
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postCompanyAction(Request $request)
    {
        $companyData = $this->retrieveCompanyData($request);
        $this->validateCompanyData($companyData);

        /** @var $companyManager CompanyManager */
        $companyManager = $this->get('wnttapi.manager.company');
        $company = $companyManager->createDocument($companyData, ['name' => $companyData['name']]);

        $view = $this->view($company, 201);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Updates Company object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="company id"}
     *   },
     *   parameters={
     *      {"name"="name", "dataType"="string", "description"="company name", "required"=true},
     *      {"name"="websiteUrl", "dataType"="string", "description"="company's website URL", "required"=false},
     *      {"name"="logoUrl", "dataType"="string", "description"="company's logo URL", "required"=false}
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putCompanyAction(Request $request, $id)
    {
        /** @var $company Company */
        $company = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->find($id);

        if (empty($company)) {
            throw $this->createNotFoundException('No company found for id '.$id);
        }

        $companyData = $this->retrieveCompanyData($request);
        $this->validateCompanyData($companyData);

        /** @var $companyManager CompanyManager */
        $companyManager = $this->get('wnttapi.manager.company');
        $company = $companyManager->updateDocument($company, $companyData);

        $view = $this->view($company, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes Company object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="company id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deleteCompanyAction($id)
    {
        /** @var $company Company */
        $company = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->find($id);

        if (empty($company)) {
            throw $this->createNotFoundException('No company found for id '.$id);
        }

        /** @var $companyManager CompanyManager */
        $companyManager = $this->get('wnttapi.manager.company');
        $companyManager->deleteDocument($company);

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    protected function retrieveCompanyData(Request $request)
    {
        return [
            'name' => $request->get('name'),
            'websiteUrl' => $request->get('websiteUrl'),
            'logoUrl' => $request->get('logoUrl'),
        ];
    }

    protected function validateCompanyData($companyData)
    {
        if (empty($companyData['name'])) {
            throw new HttpException(400, 'Missing required parameters: name');
        }
    }
}