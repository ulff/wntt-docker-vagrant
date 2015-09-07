<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Event;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\EventManager;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class EventController extends AbstractWnttRestController
{
    /**
     * Returns collection of Event objects.
     *
     * @QueryParam(name="noPaging", nullable=true, default=false, description="set to true if you want to retrieve all records without paging")
     *
     * @param ParamFetcher $paramFetcher
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
    public function getEventsAction(ParamFetcher $paramFetcher, Request $request)
    {
        $events = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Event')
            ->findAll();

        $paginator  = $this->get('knp_paginator');
        $paginatedEvents = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1),
            $paramFetcher->get('noPaging') === 'true' ? PHP_INT_MAX : $this->container->getParameter('api_list_items_per_page')
        );

        $view = $this->view($paginatedEvents, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Event object by given ID.
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
        /** @var $event Event */
        $event = $this->verifyDocumentExists($id, 'Event');

        $view = $this->view($event, 200);
        return $this->handleView($view);
    }
    
    /**
     * Creates new Event object. ROLE_ADMIN is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Creates Event object",
     *   parameters={
     *      {"name"="name", "dataType"="string", "description"="event name", "required"=true},
     *      {"name"="location", "dataType"="string", "description"="event's location", "required"=false},
     *      {"name"="dateStart", "dataType"="string", "description"="event's starting date", "required"=true},
     *      {"name"="dateEnd", "dataType"="string", "description"="event's ending date", "required"=true}
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postEventAction(Request $request)
    {
        $eventData = $this->retrieveEventData($request);
        $this->validateEventData($eventData);

        try {
            /** @var $eventManager EventManager */
            $eventManager = $this->get('wnttapi.manager.event');
            $event = $eventManager->createDocument($eventData, [
                'name' => $eventData['name'],
                'dateStart' => $eventData['dateStart'],
                'dateEnd' => $eventData['dateEnd']
            ]);
        } catch(DuplicatedDocumentException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($event, 201);
        return $this->handleView($view);
    }

    /**
     * Updates existing Event object with given ID. ROLE_ADMIN is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Updates Event object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="event id"}
     *   },
     *   parameters={
     *      {"name"="name", "dataType"="string", "description"="event name", "required"=true},
     *      {"name"="location", "dataType"="string", "description"="event's location", "required"=false},
     *      {"name"="dateStart", "dataType"="string", "description"="event's starting date", "required"=true},
     *      {"name"="dateEnd", "dataType"="string", "description"="event's ending date", "required"=true}
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putEventAction(Request $request, $id)
    {
        /** @var $event Event */
        $event = $this->verifyDocumentExists($id, 'Event');

        $eventData = $this->retrieveEventData($request);
        $this->validateEventData($eventData);

        try {
            /** @var $eventManager EventManager */
            $eventManager = $this->get('wnttapi.manager.event');
            $event = $eventManager->updateDocument($event, $eventData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($event, 200);
        return $this->handleView($view);
    }

    /**
     * Deletes existing Event object with given ID. ROLE_ADMIN is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes Event object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="event id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deleteEventAction($id)
    {
        /** @var $event Event */
        $event = $this->verifyDocumentExists($id, 'Event');

        try {
            /** @var $eventManager EventManager */
            $eventManager = $this->get('wnttapi.manager.event');
            $eventManager->deleteDocument($event);
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * Returns collection of Presentation objects by given Event ID.
     *
     * @QueryParam(name="company", nullable=true, description="set company's ID to filter presentations of one company")
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
     *  description="Returns collection of Presentation objects by given Event ID.",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getEventPresentationsAction(ParamFetcher $paramFetcher, Request $request, $eventId)
    {
        /** @var $event Event */
        $event = $this->verifyDocumentExists($eventId, 'Event');

        $companyId = $paramFetcher->get('company', null);
        if(!empty($companyId)) {
            $this->verifyDocumentExists($companyId, 'Company');
        }

        $includeProperties = $paramFetcher->get('include');
        $view = $this->createViewWithSerializationContext($includeProperties);

        $searchParams = $paramFetcher->get('search');

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

    protected function retrieveEventData(Request $request)
    {
        return [
            'name' => $request->get('name'),
            'location' => $request->get('location'),
            'dateStart' => $request->get('dateStart'),
            'dateEnd' => $request->get('dateEnd'),
        ];
    }

    protected function validateEventData($eventData)
    {
        if (empty($eventData['name'])) {
            throw new HttpException(400, 'Missing required parameters: name');
        }
        if (empty($eventData['dateStart'])) {
            throw new HttpException(400, 'Missing required parameters: dateStart');
        }
        if (empty($eventData['dateEnd'])) {
            throw new HttpException(400, 'Missing required parameters: dateEnd');
        }
    }
}
