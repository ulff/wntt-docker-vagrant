<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

class UserManager extends AbstractDocumentManager
{
    /** @var $baseUserManager BaseUserManager */
    protected $baseUserManager;

    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'User';
        parent::__construct($documentManager);
    }

    /**
     * @param array $documentData
     * @return Document
     */
    public function createDocument(array $documentData, array $checkIfExistsBy)
    {
        $document = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttUserBundle:'.$this->documentName)
            ->findOneBy($checkIfExistsBy);

        if (empty($document)) {
            $document = $this->createDocumentFromRequest($documentData);
            $this->documentManager->persist($document);
            $this->documentManager->flush();
        }

        return $document;
    }

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
        /** @var $user User */
        $user->setUsername($userData['username']);
        $user->setEmail($userData['email']);
        $user->setPlainPassword($userData['password']);
        $user->setPhoneNumber($userData['phoneNumber']);
        $user->setEnabled(true);

        $companyId = $userData['company'];
        if(!empty($companyId)) {
            $company = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
                ->findOneById($userData['company']);
            $user->setCompany($company);
        }

        $userRoles = ['ROLE_USER'];
        if($userData['isAdmin'] == 'true') {
            $userRoles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($userRoles);
    }
}