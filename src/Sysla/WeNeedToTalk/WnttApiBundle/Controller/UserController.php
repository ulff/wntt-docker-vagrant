<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\UserExistsException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\UserManager;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sysla\WeNeedToTalk\WnttApiBundle\Service\EmailDispatcher;

class UserController extends AbstractWnttRestController
{
    /**
     * Returns collection of User objects.
     *
     * @QueryParam(name="username", nullable=true, requirements="\w+")
     * @QueryParam(name="noPaging", nullable=true, default=false, description="set to true if you want to retrieve all records without paging")
     *
     * @param ParamFetcher $paramFetcher
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
    public function getUsersAction(ParamFetcher $paramFetcher, Request $request)
    {
        $queryParams = [];
        $username = $paramFetcher->get('username');
        if(!empty($username)) {
            $queryParams['username'] = $username;
        }

        $users = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
            ->findBy($queryParams);

        $paginator  = $this->get('knp_paginator');
        $paginatedUsers = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1),
            $paramFetcher->get('noPaging') === 'true' ? PHP_INT_MAX : $this->container->getParameter('api_list_items_per_page')
        );

        $view = $this->view($paginatedUsers, 200);
        return $this->handleView($view);
    }

    /**
     * Returns User object by given ID.
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
            ->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No product found for id '.$id);
        }

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * Creates new User object. Works without user context.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Creates User object",
     *   parameters={
     *      {"name"="username", "dataType"="string", "description"="user name", "required"=true},
     *      {"name"="email", "dataType"="string", "description"="user's email", "required"=true},
     *      {"name"="password", "dataType"="string", "description"="user's password", "required"=true},
     *      {"name"="company", "dataType"="string", "description"="user's company ID", "required"=false},
     *      {"name"="isAdmin", "dataType"="string", "description"="user has admin priviledges", "required"=false, "format"="true|false"},
     *      {"name"="phoneNumber", "dataType"="string", "description"="user's phone number", "required"=false},
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

        try {
            /** @var $userManager UserManager */
            $userManager = $this->get('wnttapi.manager.user');
            $baseUserManager = $this->container->get('fos_user.user_manager');
            $userManager->setBaseUserManager($baseUserManager);
            $userManager->setTokenGenerator($this->get('fos_user.util.token_generator'));
            $user = $userManager->createDocument($userData, [
                'username' => $userData['username']
            ]);

            /** @var $emailDispatcher EmailDispatcher */
            $emailDispatcher = $this->get('wnttapi.service.email_dispatcher');
            $emailDispatcher->sendUserConfirmationEmail($user);
        } catch(UserExistsException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Swift_TransportException $e) {
            throw new HttpException(500, 'User was created, but some problems with sending confirmation email occured');
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($user, 201);
        return $this->handleView($view);
    }

    /**
     * Updates existing User object with given ID. ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Updates User object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="user id"}
     *   },
     *   parameters={
     *      {"name"="username", "dataType"="string", "description"="user name", "required"=true},
     *      {"name"="email", "dataType"="string", "description"="user's email", "required"=true},
     *      {"name"="password", "dataType"="string", "description"="user's password", "required"=true},
     *      {"name"="company", "dataType"="string", "description"="user's company ID", "required"=false},
     *      {"name"="isAdmin", "dataType"="string", "description"="true or false", "required"=false},
     *      {"name"="phoneNumber", "dataType"="string", "description"="user's phone number", "required"=false},
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
            ->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (empty($user)) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $this->checkPermission($id);

        $userData = $this->retrieveUserData($request);
        $this->validateUserData($userData);

        try {
            /** @var $userManager UserManager */
            $userManager = $this->get('wnttapi.manager.user');
            $user = $userManager->updateDocument($user, $userData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * Selectively updates existing User object with given ID, affecting only properties defined in request.
     * ROLE_USER is minimum required role to perform this action.
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Selectively updates defined properties of User object",
     *   requirements={
     *      {"name"="id", "dataType"="string", "description"="user id"}
     *   },
     *   parameters={
     *      {"name"="username", "dataType"="string", "description"="user name", "required"=false},
     *      {"name"="email", "dataType"="string", "description"="user's email", "required"=false},
     *      {"name"="password", "dataType"="string", "description"="user's password", "required"=false},
     *      {"name"="company", "dataType"="string", "description"="user's company ID", "required"=false},
     *      {"name"="isAdmin", "dataType"="string", "description"="true or false", "required"=false},
     *      {"name"="phoneNumber", "dataType"="string", "description"="user's phone number", "required"=false},
     *   },
     *   statusCodes={
     *      200="Returned when successfully updated",
     *      400="Returned on invalid request (e.g. missing obligatory param, or invalid referenced object ID)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *      404="Returned when the object with given ID is not found"
     *   }
     * )
     */
    public function patchUserAction(Request $request, $id)
    {
        /** @var $user User */
        $user = $this->get('doctrine_mongodb')
            ->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (empty($user)) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $this->checkPermission($id);

        $userData = $this->retrievePatchUserData($request);
        $this->validatePatchUserData($userData);

        try {
            /** @var $userManager UserManager */
            $userManager = $this->get('wnttapi.manager.user');
            $user = $userManager->patchDocument($user, $userData);
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * Deletes existing User object with given ID. ROLE_USER is minimum required role to perform this action.
     *
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
            ->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
            ->find($id);

        if (empty($user)) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        $this->checkPermission($id);

        try {
            /** @var $userManager UserManager */
            $userManager = $this->get('wnttapi.manager.user');
            $userManager->deleteDocument($user);
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request');
        }

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

        if(!empty($userData['company'])) {
            $company = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
                ->findOneById($userData['company']);
            if (empty($company)) {
                throw new HttpException(400, "Invalid parameter: company with ID: '{$userData['company']}' not found!");
            }
        }
    }

    protected function retrievePatchUserData(Request $request)
    {
        $data = [];

        if($request->get('username') !== null) {
            $data['username'] = $request->get('username');
        }
        if($request->get('email') !== null) {
            $data['email'] = $request->get('email');
        }
        if($request->get('password') !== null) {
            $data['password'] = $request->get('password');
        }
        if($request->get('phoneNumber') !== null) {
            $data['phoneNumber'] = $request->get('phoneNumber');
        }
        if($request->get('company') !== null) {
            $data['company'] = $request->get('company');
        }
        if($request->get('isAdmin') !== null) {
            $data['isAdmin'] = $request->get('isAdmin');
        }

        return $data;
    }

    protected function validatePatchUserData($userData)
    {
        $documentManager = $this->get('doctrine_mongodb')->getManager();

        if (isset($userData['username']) && empty($userData['username'])) {
            throw new HttpException(400, 'Missing required parameters: username');
        }
        if (isset($userData['password']) && empty($userData['password'])) {
            throw new HttpException(400, 'Missing required parameters: password');
        }
        if (isset($userData['email']) && empty($userData['email'])) {
            throw new HttpException(400, 'Missing required parameters: email');
        }

        if(!empty($userData['company'])) {
            $company = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
                ->findOneById($userData['company']);
            if (empty($company)) {
                throw new HttpException(400, "Invalid parameter: company with ID: '{$userData['company']}' not found!");
            }
        }
    }

    protected function checkPermission($documentId)
    {
        $permissionVerifier = $this->get('wnttapi.service.permission_verifier');
        if(!$permissionVerifier->hasPermission('User', $this->getUser(), $documentId)) {
            throw $this->createAccessDeniedException('Cannot affect not your account!');
        }
    }
}
