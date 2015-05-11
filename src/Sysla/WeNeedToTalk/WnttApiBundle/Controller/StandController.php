<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\StandManager;

class StandController extends FOSRestController
{
    /**
     * Returns collection of Stand objects
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns collection of Stand objects",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *     }
     * )
     */
    public function getStandsAction()
    {
        $stands = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->findAll();

        $view = $this->view($stands, 200);
        return $this->handleView($view);
    }

    /**
     * Returns Stand object by given ID
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns Stand object by given ID",
     *  statusCodes={
     *         200="Returned when successful",
     *         401="Returned when client is requesting without or with invalid access_token",
     *         404="Returned when the object with given ID is not found"
     *     }
     * )
     */
    public function getStandAction($id)
    {
        $stand = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->find($id);

        if (!$stand) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($stand, 200);
        return $this->handleView($view);
    }
    
    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Creates Stand object",
     *   parameters={
     *      {"name"="number", "dataType"="string", "description"="stand number", "required"=true},
     *      {"name"="stand", "dataType"="string", "description"="stand's event ID", "required"=true},
     *      {"name"="hall", "dataType"="string", "description"="stand's hall", "required"=false},
     *      {"name"="company", "dataType"="string", "description"="stand's company ID", "required"=false},
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postStandAction(Request $request)
    {
        $standData = $this->retrieveStandData($request);
        $this->validateStandData($standData);

        /** @var $standManager StandManager */
        $standManager = $this->get('wnttapi.manager.stand');
        $stand = $standManager->createDocument($standData, [
            'number' => $standData['number'],
            'event.id' => $standData['event']
        ]);

        $view = $this->view($stand, 201);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Updates Stand object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="stand id"}
     *   },
     *   parameters={
     *      {"name"="number", "dataType"="string", "description"="stand number", "required"=true},
     *      {"name"="stand", "dataType"="string", "description"="stand's event ID", "required"=true},
     *      {"name"="hall", "dataType"="string", "description"="stand's hall", "required"=false},
     *      {"name"="company", "dataType"="string", "description"="stand's company ID", "required"=false},
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putStandAction(Request $request, $id)
    {
        /** @var $stand Stand */
        $stand = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->find($id);

        if (empty($stand)) {
            throw $this->createNotFoundException('No stand found for id '.$id);
        }

        $standData = $this->retrieveStandData($request);
        $this->validateStandData($standData);

        /** @var $standManager StandManager */
        $standManager = $this->get('wnttapi.manager.stand');
        $stand = $standManager->updateDocument($stand, $standData);

        $view = $this->view($stand, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes Stand object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="stand id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deleteStandAction($id)
    {
        /** @var $stand Stand */
        $stand = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->find($id);

        if (empty($stand)) {
            throw $this->createNotFoundException('No stand found for id '.$id);
        }

        /** @var $standManager StandManager */
        $standManager = $this->get('wnttapi.manager.stand');
        $standManager->deleteDocument($stand);

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    protected function retrieveStandData(Request $request)
    {
        return [
            'number' => $request->get('number'),
            'hall' => $request->get('hall'),
            'event' => $request->get('event'),
            'company' => $request->get('company')
        ];
    }

    protected function validateStandData($standData)
    {
        $documentManager = $this->get('doctrine_mongodb')->getManager();

        if (empty($standData['number'])) {
            throw new HttpException(400, 'Missing required parameters: number');
        }
        if (empty($standData['event'])) {
            throw new HttpException(400, 'Missing required parameters: event');
        }

        $company = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->findOneById($standData['company']);
        if(empty($company)) {
            throw new HttpException(400, "Invalid parameter: company with ID: '{$standData['company']}' not found!");
        }

        $event = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->findOneById($standData['event']);
        if(empty($event)) {
            throw new HttpException(400, "Invalid parameter: event with ID: '{$standData['event']}' not found!");
        }
    }
}