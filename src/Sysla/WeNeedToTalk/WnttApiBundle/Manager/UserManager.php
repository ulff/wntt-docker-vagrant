<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\UserExistsException;
use FOS\UserBundle\Util\TokenGeneratorInterface;

class UserManager extends AbstractDocumentManager
{
    /** @var $baseUserManager BaseUserManager */
    protected $baseUserManager;

    /** @var $tokenGenerator TokenGeneratorInterface */
    protected $tokenGenerator;

    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'User';
        parent::__construct($documentManager);
    }

    /**
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function setTokenGenerator(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param array $documentData
     * @return Document
     */
    public function createDocument(array $documentData, array $checkIfExistsBy)
    {
        $document = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttUserBundle:'.$this->documentName)
            ->findOneBy($checkIfExistsBy);

        if (!empty($document)) {
            throw new UserExistsException();
        }

        $this->validateDocumentData($documentData);
        $document = $this->createDocumentFromRequest($documentData);
        $this->documentManager->persist($document);
        $this->documentManager->flush();

        return $document;
    }

    /**
     * @param Document $document
     * @param array $documentData
     * @return Document
     */
    public function patchDocument(Document $document, array $documentData)
    {
        $this->patchDocumentFromRequest($document, $documentData);
        $this->documentManager->persist($document);
        $this->documentManager->flush();

        return $document;
    }

    /**
     * @param BaseUserManager $baseUserManager
     */
    public function setBaseUserManager(BaseUserManager $baseUserManager)
    {
        $this->baseUserManager = $baseUserManager;
    }

    protected function createDocumentFromRequest(array $userData)
    {
        /** @var $user User */
        $user = $this->baseUserManager->createUser();
        $this->updateDocumentFromRequest($user, $userData);

        return $user;
    }

    protected function updateDocumentFromRequest(Document $user, array $userData)
    {
        $userId = $user->getId();

        /** @var $user User */
        $user->setUsername($userData['username']);
        $user->setEmail($userData['email']);
        $user->setPlainPassword($userData['password']);
        $user->setPhoneNumber($userData['phoneNumber']);

        if(empty($userId)) {
            $user->setEnabled(false);
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $companyId = $userData['company'];
        if(!empty($companyId)) {
            $company = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
                ->findOneById($userData['company']);
            $user->setCompany($company);
        }

        $userRoles = ['ROLE_USER'];
        if($userData['isAdmin'] == 'true') {
            $userRoles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($userRoles);
    }

    protected function patchDocumentFromRequest(Document $user, array $userData)
    {
        /** @var $user User */
        if(isset($userData['username'])) {
            $user->setUsername($userData['username']);
        }
        if(isset($userData['email'])) {
            $user->setEmail($userData['email']);
        }
        if(isset($userData['password'])) {
            $user->setPlainPassword($userData['password']);
        }
        if(isset($userData['phoneNumber'])) {
            $user->setPhoneNumber($userData['phoneNumber']);
        }

        if(isset($userData['company'])) {
            $companyId = $userData['company'];
            if (!empty($companyId)) {
                $company = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
                    ->findOneById($userData['company']);
                $user->setCompany($company);
            }
        }

        if(isset($userData['isAdmin'])) {
            $userRoles = ['ROLE_USER'];
            if ($userData['isAdmin'] == 'true') {
                $userRoles[] = 'ROLE_ADMIN';
            }
            $user->setRoles($userRoles);
        }
    }

    protected function validateDocumentData(array $userData, Document $user = null)
    {
    }
}