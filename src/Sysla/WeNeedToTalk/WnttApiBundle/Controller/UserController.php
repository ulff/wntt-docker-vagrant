<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\UserManager;

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
        $user = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Creates User object",
     *   parameters={
     *      {"name"="number", "dataType"="string", "description"="user number", "required"=true},
     *      {"name"="user", "dataType"="string", "description"="user's event ID", "required"=true},
     *      {"name"="hall", "dataType"="string", "description"="user's hall", "required"=false},
     *      {"name"="company", "dataType"="string", "description"="user's company ID", "required"=false},
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function postUserAction(Request $request)
    {
        $userData = $this->retrieveUserData($request);
        $this->validateUserData($userData);

        /** @var $userManager UserManager */
        $userManager = $this->get('wnttapi.manager.user');
        $baseUserManager = $this->container->get('fos_user.user_manager');
        $userManager->setBaseUserManager($baseUserManager);
        $user = $userManager->createDocument($userData, [
            'username' => $userData['username']
        ]);

        $view = $this->view($user, 201);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Updates User object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="user id"}
     *   },
     *   parameters={
     *      {"name"="number", "dataType"="string", "description"="user number", "required"=true},
     *      {"name"="user", "dataType"="string", "description"="user's event ID", "required"=true},
     *      {"name"="hall", "dataType"="string", "description"="user's hall", "required"=false},
     *      {"name"="company", "dataType"="string", "description"="user's company ID", "required"=false},
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function putUserAction(Request $request, $id)
    {
        /** @var $user User */
        $user = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (empty($user)) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $userData = $this->retrieveUserData($request);
        $this->validateUserData($userData);

        /** @var $userManager UserManager */
        $userManager = $this->get('wnttapi.manager.user');
        $user = $userManager->updateDocument($user, $userData);

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Deletes User object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="user id"}
     *   },
     *   statusCodes={
     *      200="Returned when successfully deleted",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function deleteUserAction($id)
    {
        /** @var $user User */
        $user = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (empty($user)) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        /** @var $userManager UserManager */
        $userManager = $this->get('wnttapi.manager.user');
        $userManager->deleteDocument($user);

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    protected function retrieveUserData(Request $request)
    {
        return [
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
            'phoneNumber' => $request->get('phoneNumber'),
            'company' => $request->get('company'),
            'isAdmin' => $request->get('isAdmin')
        ];
    }

    protected function validateUserData($userData)
    {
        $documentManager = $this->get('doctrine_mongodb')->getManager();

        if (empty($userData['username'])) {
            throw new HttpException(400, 'Missing required parameters: username');
        }
        if (empty($userData['password'])) {
            throw new HttpException(400, 'Missing required parameters: password');
        }
        if (empty($userData['email'])) {
            throw new HttpException(400, 'Missing required parameters: email');
        }

        $company = $documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->findOneById($userData['company']);
        if(empty($company)) {
            throw new HttpException(400, "Invalid parameter: company with ID: '{$userData['company']}' not found!");
        }
    }
}