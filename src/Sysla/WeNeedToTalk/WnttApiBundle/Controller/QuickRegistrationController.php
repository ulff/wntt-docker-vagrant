<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DuplicatedDocumentException;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\UserExistsException;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\UserManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\CompanyManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Manager\PresentationManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Service\EmailDispatcher;

class QuickRegistrationController extends FOSRestController
{
    /**
     * Non-RESTful endpoint for quick registration of user, company and presentation.
     *
     * @Post("/quick_registration/")
     *
     * @ApiDoc(
     *   resource=true,
     *   description="Non-RESTful endpoint for quick registration of user, company and presentation",
     *   parameters={
     *      {"name"="user_email", "dataType"="string", "description"="user's email and username", "required"=true},
     *      {"name"="user_fullname", "dataType"="string", "description"="user's firstname and surname", "required"=true},
     *      {"name"="user_phone", "dataType"="string", "description"="user's (contact person's) phone number", "required"=false},
     *      {"name"="company_name", "dataType"="string", "description"="company's name", "required"=true},
     *      {"name"="presentation_name", "dataType"="string", "description"="presentations's name", "required"=true},
     *      {"name"="presentation_hall", "dataType"="string", "description"="presentations's hall", "required"=false},
     *      {"name"="presentation_number", "dataType"="string", "description"="presentations's stand number", "required"=false},
     *      {"name"="presentation_event", "dataType"="string", "description"="presentations's event ID", "required"=true},
     *      {"name"="presentation_categories", "dataType"="string/array", "description"="presentations's category ID (or array with category IDs)", "required"=false}
     *   },
     *   statusCodes={
     *      201="Returned when successfully created",
     *      400="Returned on invalid request (e.g. missing obligatory param)",
     *      401="Returned when client is requesting without or with invalid access_token",
     *   }
     * )
     */
    public function quickRegistrationAction(Request $request)
    {
        /** @var $company Company */
        $company = $this->registerCompany($request);

        /** @var $user User */
        $user = $this->registerUser($request, $company);

        /** @var $presentation Presentation */
        $presentation = $this->registerPresentation($request, $company, $user);

        /** @var $emailDispatcher EmailDispatcher */
        $emailDispatcher = $this->get('wnttapi.service.email_dispatcher');
        $emailDispatcher->sendNewCompanyRegsiteredNotification($company);
        $emailDispatcher->sendUserConfirmationEmail($user);

        $aggregate = [];
        $aggregate['user'] = $user;
        $aggregate['company'] = $company;
        $aggregate['presentation'] = $presentation;

        $view = $this->view($aggregate, 201);
        return $this->handleView($view);
    }

    protected function registerCompany(Request $request)
    {
        try {
            $companyData = $this->retrieveCompanyData($request);
            $this->validateCompanyData($companyData);

            /** @var $companyManager CompanyManager */
            $companyManager = $this->get('wnttapi.manager.company');
            $company = $companyManager->createDocument($companyData, ['name' => $companyData['name']]);
        } catch(DuplicatedDocumentException $e) {
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            throw new HttpException(400, $e->getMessage());
        } catch(\Swift_TransportException $e) {
            throw new HttpException(500, 'Company was created, but some problems with sending email occured');
        } catch(\Exception $e) {
            throw new HttpException(500, 'Unknown error occured during processing request (on registering company)');
        }

        return $company;
    }

    protected function registerUser(Request $request, Company $company)
    {
        try {
            $userData = $this->retrieveUserData($request);
            $this->validateUserData($userData);
            $userData['company'] = $company->getId();

            /** @var $userManager UserManager */
            $userManager = $this->get('wnttapi.manager.user');
            $baseUserManager = $this->container->get('fos_user.user_manager');
            $userManager->setBaseUserManager($baseUserManager);
            $userManager->setTokenGenerator($this->get('fos_user.util.token_generator'));
            $user = $userManager->createDocument($userData, [
                'username' => $userData['username']
            ]);
        } catch(UserExistsException $e) {
            $this->rollbackCompany($company);
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            $this->rollbackCompany($company);
            throw new HttpException(400, $e->getMessage());
        } catch(\Swift_TransportException $e) {
            $this->rollbackCompany($company);
            throw new HttpException(500, 'User was created, but some problems with sending confirmation email occured');
        } catch(\Exception $e) {
            $this->rollbackCompany($company);
            throw new HttpException(500, 'Unknown error occured during processing request (on registering user)');
        }

        return $user;
    }

    protected function registerPresentation(Request $request, Company $company, User $user)
    {
        try {
            $presentationData = $this->retrievePresentationData($request);
            $this->validatePresentationData($presentationData);
            $presentationData['company'] = $company->getId();

            /** @var $presentationManager PresentationManager */
            $presentationManager = $this->get('wnttapi.manager.presentation');
            $presentation = $presentationManager->createDocument($presentationData, ['videoUrl' => $presentationData['videoUrl']]);
        } catch(DuplicatedDocumentException $e) {
            $this->rollbackCompany($company);
            $this->rollbackUser($user);
            throw new HttpException(409, $e->getMessage());
        } catch(DocumentValidationException $e) {
            $this->rollbackCompany($company);
            $this->rollbackUser($user);
            throw new HttpException(400, $e->getMessage());
        } catch(\Exception $e) {
            $this->rollbackCompany($company);
            $this->rollbackUser($user);
            throw new HttpException(500, 'Unknown error occured during processing request (on registering presentation)');
        }

        return $presentation;
    }

    protected function rollbackCompany($company)
    {
        /** @var $companyManager CompanyManager */
        $companyManager = $this->get('wnttapi.manager.company');
        $companyManager->deleteDocument($company);
    }

    protected function rollbackUser($user)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('wnttapi.manager.user');
        $userManager->deleteDocument($user);
    }

    protected function retrieveCompanyData(Request $request)
    {
        return [
            'name' => $request->get('company_name'),
            'websiteUrl' => null,
            'logoUrl' => null
        ];
    }

    protected function retrieveUserData(Request $request)
    {
        return [
            'username' => $request->get('user_email'),
            'email' => $request->get('user_email'),
            'phoneNumber' => $request->get('user_phone'),
            'fullName' => $request->get('user_fullname'),
            'isContactPerson' => true,
            'isAdmin' => false,
            'password' => null
        ];
    }

    protected function retrievePresentationData(Request $request)
    {
        return [
            'name' => $request->get('presentation_name'),
            'hall' => $request->get('presentation_hall'),
            'number' => $request->get('presentation_number'),
            'event' => $request->get('presentation_event'),
            'categories' => $request->get('presentation_categories'),
            'videoUrl' => null,
            'description' => null,
            'isPremium' => false
        ];
    }

    protected function validateCompanyData($companyData)
    {
        if (empty($companyData['name'])) {
            throw new DocumentValidationException('Missing required parameters: company_name');
        }
    }

    protected function validateUserData($userData)
    {
        if (empty($userData['email'])) {
            throw new DocumentValidationException('Missing required parameters: user_email');
        }
        if (empty($userData['fullName'])) {
            throw new DocumentValidationException('Missing required parameters: user_fullname');
        }
    }

    protected function validatePresentationData($presentationData)
    {
        $documentManager = $this->get('doctrine_mongodb')->getManager();

        if (empty($presentationData['name'])) {
            throw new DocumentValidationException('Missing required parameters: presentation_name');
        }
        if (empty($presentationData['event'])) {
            throw new DocumentValidationException('Missing required parameters: presentation_event');
        }

        $categoryIds = $presentationData['categories'];
        if(is_array($categoryIds)) {
            foreach($categoryIds as $categoryId) {
                $category = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                    ->findOneById($categoryId);
                if(empty($category)) {
                    throw new DocumentValidationException("Invalid parameter: category with ID: '$categoryId' not found!");
                }
            }
        } elseif (!empty($categoryIds)) {
            $category = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                ->findOneById($presentationData['categories']);
            if(empty($category)) {
                throw new DocumentValidationException("Invalid parameter: category with ID: '{$categoryIds}' not found!");
            }
        }

        $event = $documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Event')
            ->findOneById($presentationData['event']);
        if(empty($event)) {
            throw new DocumentValidationException("Invalid parameter: event with ID: '{$presentationData['event']}' not found!");
        }
    }
}
