<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\CompanyManager;

class CompanyController extends AbstractWnttRestController
{
    /**
     * Returns collection of Company objects.
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
     * Returns Company object by given ID.
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
     * Creates new Company object. ROLE_USER is minimum required role to perform this action.
     *
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

        try {
            /** @var $companyManager CompanyManager */
            $companyManager = $this->get('wnttapi.manager.company');
            $company = $companyManager->createDocument($companyData, ['name' => $companyData['name']]);
        } catch(DuplicatedDocumentException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($company, 201);
        return $this->handleView($view);
    }

    /**
     * Updates existing Company object with given ID. ROLE_USER is minimum required role to perform this action.
     *
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

        $this->checkPermission($id);

        $companyData = $this->retrieveCompanyData($request);
        $this->validateCompanyData($companyData);

        try {
            /** @var $companyManager CompanyManager */
            $companyManager = $this->get('wnttapi.manager.company');
            $company = $companyManager->updateDocument($company, $companyData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($company, 200);
        return $this->handleView($view);
    }

    /**
     * Deletes existing Company object with given ID. ROLE_USER is minimum required role to perform this action.
     *
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

        $this->checkPermission($id);

        try {
            /** @var $companyManager CompanyManager */
            $companyManager = $this->get('wnttapi.manager.company');
            $companyManager->deleteDocument($company);
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

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

    protected function checkPermission($documentId)
    {
        $permissionVerifier = $this->get('wnttapi.service.permission_verifier');
        if(!$permissionVerifier->hasPermission('Company', $this->getUser(), $documentId)) {
            throw $this->createAccessDeniedException('Cannot affect not your company!');
        }
    }
}
