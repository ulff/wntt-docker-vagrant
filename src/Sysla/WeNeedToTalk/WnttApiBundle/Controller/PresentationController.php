<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\PresentationManager;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class PresentationController extends AbstractWnttRestController
{
    /**
     * Returns collection of Presentation objects.
     *
     * @QueryParam(name="include", nullable=true, default=null, array=true)
     * @QueryParam(name="search", nullable=true, default=null, array=true)
     * @QueryParam(name="noPaging", nullable=true, default=false, description="set to true if you want to retrieve all records without paging")
     *
     * @param ParamFetcher $paramFetcher
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
    public function getPresentationsAction(ParamFetcher $paramFetcher, Request $request)
    {
        $includeProperties = $paramFetcher->get('include');
        $view = $this->createViewWithSerializationContext($includeProperties);

        $searchParams = $paramFetcher->get('search');

        $presentations = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->findBySearchParams($searchParams);

        $paginator  = $this->get('knp_paginator');
        $paginatedPresentations = $paginator->paginate(
            $presentations,
            $request->query->getInt('page', 1),
            $paramFetcher->get('noPaging') === 'true' ? PHP_INT_MAX : $this->container->getParameter('api_list_items_per_page')
        );

        $view->setData($paginatedPresentations);
        return $this->handleView($view);
    }

    /**
     * Returns Presentation object by given ID.
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
        $presentation = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->find($id);

        if (!$presentation) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($presentation, 200);
        return $this->handleView($view);
    }

    /**
     * Creates new Presentation object. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Creates Presentation object",
     *   parameters={
     *      {"name"="videoUrl", "dataType"="string", "description"="presentation name", "required"=true},
     *      {"name"="company", "dataType"="string", "description"="presentation's company ID", "required"=true},
     *      {"name"="stand", "dataType"="string", "description"="presentation's stand ID", "required"=true},
     *      {"name"="name", "dataType"="string", "description"="presentation's name", "required"=true},
     *      {"name"="description", "dataType"="string", "description"="presentation's descrption", "required"=false},
     *      {"name"="isPremium", "dataType"="boolean", "description"="is presentation premium (true) or free (false)", "required"=false},
     *      {"name"="categories", "dataType"="string/array", "description"="one on many category IDs", "required"=false},
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postPresentationAction(Request $request)
    {
        $presentationData = $this->retrievePresentationData($request);
        $this->checkPermission($presentationData['company']);
        $this->validatePresentationData($presentationData);

        try {
            /** @var $presentationManager PresentationManager */
            $presentationManager = $this->get('wnttapi.manager.presentation');
            $presentation = $presentationManager->createDocument($presentationData, ['videoUrl' => $presentationData['videoUrl']]);
        } catch(DuplicatedDocumentException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($presentation, 201);
        return $this->handleView($view);
    }

    /**
     * Updates existing Presentation object with given ID. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Updates Presentation object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="presentation id"}
     *   },
     *   parameters={
     *      {"name"="videoUrl", "dataType"="string", "description"="presentation name", "required"=true},
     *      {"name"="company", "dataType"="string", "description"="presentation's company ID", "required"=true},
     *      {"name"="stand", "dataType"="string", "description"="presentation's stand ID", "required"=true},
     *      {"name"="name", "dataType"="string", "description"="presentation's name", "required"=true},
     *      {"name"="description", "dataType"="string", "description"="presentation's descrption", "required"=false},
     *      {"name"="isPremium", "dataType"="boolean", "description"="is presentation premium (true) or free (false)", "required"=false},
     *      {"name"="categories", "dataType"="string/array", "description"="one on many category IDs", "required"=false},
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putPresentationAction(Request $request, $id)
    {
        /** @var $presentation Presentation */
        $presentation = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->find($id);

        if (empty($presentation)) {
            throw $this->createNotFoundException('No presentation found for id '.$id);
        }

        $presentationData = $this->retrievePresentationData($request);
        $this->checkPermission($presentationData['company']);
        $this->validatePresentationData($presentationData);

        try {
            /** @var $presentationManager PresentationManager */
            $presentationManager = $this->get('wnttapi.manager.presentation');
            $presentation = $presentationManager->updateDocument($presentation, $presentationData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($presentation, 200);
        return $this->handleView($view);
    }

    /**
     * Deletes existing Presentation object with given ID. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes Presentation object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="presentation id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deletePresentationAction($id)
    {
        /** @var $presentation Presentation */
        $presentation = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->find($id);

        if (empty($presentation)) {
            throw $this->createNotFoundException('No presentation found for id '.$id);
        }

        $this->checkPermission($presentation->getCompany()->getId());

        try {
            /** @var $presentationManager PresentationManager */
            $presentationManager = $this->get('wnttapi.manager.presentation');
            $presentationManager->deleteDocument($presentation);
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    protected function retrievePresentationData(Request $request)
    {
        return [
            'videoUrl' => $request->get('videoUrl'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'stand' => $request->get('stand'),
            'company' => $request->get('company'),
            'isPremium' => $request->get('isPremium'),
            'categories' => $request->get('categories')
        ];
    }

    protected function validatePresentationData($presentationData)
    {
        $documentManager = $this->get('doctrine_mongodb')->getManager();

        if (empty($presentationData['videoUrl'])) {
            throw new HttpException(400, 'Missing required parameters: videoUrl');
        }
        if (empty($presentationData['name'])) {
            throw new HttpException(400, 'Missing required parameters: name');
        }
        if (empty($presentationData['stand'])) {
            throw new HttpException(400, 'Missing required parameters: stand');
        }
        if (empty($presentationData['company'])) {
            throw new HttpException(400, 'Missing required parameters: company');
        }

        $categoryIds = $presentationData['categories'];
        if(is_array($categoryIds)) {
            foreach($categoryIds as $categoryId) {
                $category = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
                    ->findOneById($categoryId);
                if(empty($category)) {
                    throw new HttpException(400, "Invalid parameter: category with ID: '$categoryId' not found!");
                }
            }
        } elseif (!empty($categoryIds)) {
            $category = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
                ->findOneById($presentationData['categories']);
            if(empty($category)) {
                throw new HttpException(400, "Invalid parameter: category with ID: '{$categoryIds}' not found!");
            }
        }

        $company = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->findOneById($presentationData['company']);
        if(empty($company)) {
            throw new HttpException(400, "Invalid parameter: company with ID: '{$presentationData['company']}' not found!");
        }

        $stand = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->findOneById($presentationData['stand']);
        if(empty($stand)) {
            throw new HttpException(400, "Invalid parameter: stand with ID: '{$presentationData['stand']}' not found!");
        }
    }

    protected function checkPermission($documentId)
    {
        $permissionVerifier = $this->get('wnttapi.service.permission_verifier');
        if(!$permissionVerifier->hasPermission('Presentation', $this->getUser(), $documentId)) {
            throw $this->createAccessDeniedException('Cannot affect presentation owned by not your company!');
        }
    }
}
