<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Event;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\EventManager;

class EventController extends FOSRestController
{
    /**
     * Returns collection of Event objects.
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
    public function getEventsAction()
    {
        $events = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->findAll();

        $view = $this->view($events, 200);
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
        $event = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->find($id);

        if (!$event) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

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
        $event = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->find($id);

        if (empty($event)) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

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
        $event = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->find($id);

        if (empty($event)) {
            throw $this->createNotFoundException('No event found for id '.$id);
        }

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
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Presentation objects by given Event ID.",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getEventPresentationsAction(Request $request, $eventId)
    {
        /** @var $event Event */
        $event = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->find($eventId);

        if (empty($event)) {
            throw $this->createNotFoundException('No event found for id '.$eventId);
        }

        $presentations = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->findByEvent($eventId);

        $view = $this->view($presentations, 200);
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