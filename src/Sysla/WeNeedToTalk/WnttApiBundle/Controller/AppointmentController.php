<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\AppointmentManager;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class AppointmentController extends AbstractWnttRestController
{
    /**
     * Returns collection of Appointment objects.
     *
     * @QueryParam(name="user", nullable=true, requirements="[a-z0-9]+")
     * @QueryParam(name="event", nullable=true, requirements="[a-z0-9]+")
     * @QueryParam(name="presentation", nullable=true, requirements="[a-z0-9]+")
     * @QueryParam(name="include", nullable=true, default=null, array=true)
     * @QueryParam(name="noPaging", nullable=true, default=false, description="set to true if you want to retrieve all records without paging")
     *
     * @param ParamFetcher $paramFetcher
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Appointment objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getAppointmentsAction(ParamFetcher $paramFetcher, Request $request)
    {

        $allParams = $paramFetcher->all();
        $queryParams = [];
        if(!empty($allParams['user'])) {
            $queryParams['user.id'] = $allParams['user'];
        }
        if(!empty($allParams['event'])) {
            $queryParams['event.id'] = $allParams['event'];
        }
        if(!empty($allParams['presentation'])) {
            $queryParams['presentation.id'] = $allParams['presentation'];
        }

        $includeProperties = $paramFetcher->get('include');
        $view = $this->createViewWithSerializationContext($includeProperties);

        $appointments = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Appointment')
            ->findBy($queryParams);

        $paginator  = $this->get('knp_paginator');
        $paginatedAppointments = $paginator->paginate(
            $appointments,
            $request->query->getInt('page', 1),
            $paramFetcher->get('noPaging') === 'true' ? PHP_INT_MAX : $this->container->getParameter('api_list_items_per_page')
        );

        $view->setData($paginatedAppointments);
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
    public function optionsAppointmentAction($id)
    {
        $this->verifyDocumentExists($id, 'Appointment');

        $response = new Response();
        $response->headers->set('Allow', 'OPTIONS, GET, POST, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PUT, DELETE');

        return $response;
    }

    /**
     * Returns Appointment object by given ID.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Appointment object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getAppointmentAction($id)
    {
        /** @var $appointment Appointment */
        $appointment = $this->verifyDocumentExists($id, 'Appointment');

        $view = $this->view($appointment, 200);
        return $this->handleView($view);
    }

    /**
     * Creates new Appointment object. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Creates Appointment object",
     *   parameters={
     *      {"name"="user", "dataType"="string", "description"="user's ID", "required"=true},
     *      {"name"="event", "dataType"="string", "description"="event's ID", "required"=false},
     *      {"name"="presentation", "dataType"="string", "description"="presentation's ID", "required"=true},
     *      {"name"="isVisited", "dataType"="boolean", "description"="is presentation visited (true) or not (false)", "required"=false},
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postAppointmentAction(Request $request)
    {
        $appointmentData = $this->retrieveAppointmentData($request);
        $this->validateAppointmentData($appointmentData);

        try {
            /** @var $appointmentManager AppointmentManager */
            $appointmentManager = $this->get('wnttapi.manager.appointment');
            $appointment = $appointmentManager->createDocument($appointmentData, [
                'user.id' => $appointmentData['user'],
                'presentation.id' => $appointmentData['presentation']
            ]);
        } catch(DuplicatedDocumentException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($appointment, 201);
        return $this->handleView($view);
    }

    /**
     * Updates existing Appointment object with given ID. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Updates Appointment object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="appointment id"}
     *   },
     *   parameters={
     *      {"name"="user", "dataType"="string", "description"="user's ID", "required"=true},
     *      {"name"="event", "dataType"="string", "description"="event's ID", "required"=false},
     *      {"name"="presentation", "dataType"="string", "description"="presentation's ID", "required"=true},
     *      {"name"="isVisited", "dataType"="boolean", "description"="is presentation visited (true) or not (false)", "required"=false},
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putAppointmentAction(Request $request, $id)
    {
        /** @var $appointment Appointment */
        $appointment = $this->verifyDocumentExists($id, 'Appointment');

        $appointmentData = $this->retrieveAppointmentData($request);
        $this->checkPermission($id);
        $this->validateAppointmentData($appointmentData);

        try {
            /** @var $appointmentManager AppointmentManager */
            $appointmentManager = $this->get('wnttapi.manager.appointment');
            $appointment = $appointmentManager->updateDocument($appointment, $appointmentData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($appointment, 200);
        return $this->handleView($view);
    }

    /**
     * Deletes existing Appointment object with given ID. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes Appointment object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="appointment id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deleteAppointmentAction($id)
    {
        /** @var $appointment Appointment */
        $appointment = $this->verifyDocumentExists($id, 'Appointment');

        $this->checkPermission($id);

        try {
            /** @var $appointmentManager AppointmentManager */
            $appointmentManager = $this->get('wnttapi.manager.appointment');
            $appointmentManager->deleteDocument($appointment);
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    protected function retrieveAppointmentData(Request $request)
    {
        return [
            'user' => $request->get('user'),
            'event' => $request->get('event'),
            'presentation' => $request->get('presentation'),
            'isVisited' => $request->get('isVisited'),
        ];
    }

    protected function validateAppointmentData($appointmentData)
    {
        $documentManager = $this->get('doctrine_mongodb')->getManager();

        if (empty($appointmentData['user'])) {
            throw new HttpException(400, 'Missing required parameters: user');
        }
        if (empty($appointmentData['presentation'])) {
            throw new HttpException(400, 'Missing required parameters: presentation');
        }

        $user = $documentManager->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
            ->findOneById($appointmentData['user']);
        if(empty($user)) {
            throw new HttpException(400, "Invalid parameter: user with ID: '{$appointmentData['user']}' not found!");
        }

        if (!empty($appointmentData['event'])) {
            $event = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Event')
                ->findOneById($appointmentData['event']);
            if (empty($event)) {
                throw new HttpException(400, "Invalid parameter: event with ID: '{$appointmentData['event']}' not found!");
            }
        }

        $presentation = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Presentation')
            ->findOneById($appointmentData['presentation']);
        if(empty($presentation)) {
            throw new HttpException(400, "Invalid parameter: presentation with ID: '{$appointmentData['presentation']}' not found!");
        }
    }

    protected function checkPermission($documentId)
    {
        $permissionVerifier = $this->get('wnttapi.service.permission_verifier');
        if(!$permissionVerifier->hasPermission('Appointment', $this->getUser(), $documentId)) {
            throw $this->createAccessDeniedException('Cannot affect not your appointment!');
        }
    }
}
