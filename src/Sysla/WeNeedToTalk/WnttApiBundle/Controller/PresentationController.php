<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Event;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
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
     * @QueryParam(name="company", nullable=true, description="set company's ID to filter presentations of one company")
     * @QueryParam(name="event", nullable=true, description="set event's ID to filter presentations of one event")
     * @QueryParam(name="include", nullable=true, default=null, array=true)
     * @QueryParam(name="search", nullable=true, default=null, array=true)
     * @QueryParam(name="noPaging", nullable=true, default=false, description="set to true if you want to retrieve all records without paging")
     * @QueryParam(name="sortby", nullable=true, default=null)
     * @QueryParam(name="sortdir", nullable=true, default=null)
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

        $eventId = $paramFetcher->get('event', null);
        if(!empty($eventId)) {
            $this->verifyDocumentExists($eventId, 'Event');
        }

        $companyId = $paramFetcher->get('company', null);
        if(!empty($companyId)) {
            $this->verifyDocumentExists($companyId, 'Company');
        }

        $presentations = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Presentation')
            ->findBySearchParams($searchParams, $eventId, $companyId, [
                'sortby' => $paramFetcher->get('sortby'),
                'sortdir' => $paramFetcher->get('sortdir')
            ]);

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
     * Returns allowed HTTP methods in headers
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns allowed HTTP methods in headers",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function optionsPresentationAction($id)
    {
        /** @var $presentation Presentation */
        $presentation = $this->verifyDocumentExists($id, 'Presentation');

        $response = new Response();
        $response->headers->set('Allow', 'OPTIONS, GET, POST, PUT, DELETE');
        return $response;
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
        /** @var $presentation Presentation */
        $presentation = $this->verifyDocumentExists($id, 'Presentation');

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
     *      {"name"="event", "dataType"="string", "description"="presentation's event ID", "required"=true},
     *      {"name"="name", "dataType"="string", "description"="presentation's name", "required"=true},
     *      {"name"="hall", "dataType"="string", "description"="presentation's hall", "required"=false},
     *      {"name"="number", "dataType"="string", "description"="presentation's stand number", "required"=false},
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
     *      {"name"="event", "dataType"="string", "description"="presentation's event ID", "required"=true},
     *      {"name"="name", "dataType"="string", "description"="presentation's name", "required"=true},
     *      {"name"="hall", "dataType"="string", "description"="presentation's hall", "required"=false},
     *      {"name"="number", "dataType"="string", "description"="presentation's stand number", "required"=false},
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
        $presentation = $this->verifyDocumentExists($id, 'Presentation');

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
        $presentation = $this->verifyDocumentExists($id, 'Presentation');

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
            'hall' => $request->get('hall'),
            'number' => $request->get('number'),
            'description' => $request->get('description'),
            'event' => $request->get('event'),
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
        if (empty($presentationData['event'])) {
            throw new HttpException(400, 'Missing required parameters: event');
        }
        if (empty($presentationData['company'])) {
            throw new HttpException(400, 'Missing required parameters: company');
        }

        $categoryIds = $presentationData['categories'];
        if(is_array($categoryIds)) {
            foreach($categoryIds as $categoryId) {
                $category = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                    ->findOneById($categoryId);
                if(empty($category)) {
                    throw new HttpException(400, "Invalid parameter: category with ID: '$categoryId' not found!");
                }
            }
        } elseif (!empty($categoryIds)) {
            $category = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                ->findOneById($presentationData['categories']);
            if(empty($category)) {
                throw new HttpException(400, "Invalid parameter: category with ID: '{$categoryIds}' not found!");
            }
        }

        $company = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
            ->findOneById($presentationData['company']);
        if(empty($company)) {
            throw new HttpException(400, "Invalid parameter: company with ID: '{$presentationData['company']}' not found!");
        }

        $event = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Event')
            ->findOneById($presentationData['event']);
        if(empty($event)) {
            throw new HttpException(400, "Invalid parameter: event with ID: '{$presentationData['event']}' not found!");
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
