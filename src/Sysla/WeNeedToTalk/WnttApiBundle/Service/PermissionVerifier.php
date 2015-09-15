<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Service;

use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class PermissionVerifier
{
    protected $documentManager;

    /**
     * @param ManagerRegistry $documentManager
     */
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentManager = $documentManager->getManager();
    }

    public function hasPermission($context, User $clientUser, $documentId)
    {
        if($clientUser->hasAdminRights()) {
            return true;
        }

        $methodName = 'verify'.$context;
        if(!method_exists($this, $methodName)) {
            throw new \Exception("PermissionVerifier: method $methodName does not exist!");
        }

        return $this->$methodName($clientUser, $documentId);
    }

    protected function verifyUser(User $clientUser, $documentId)
    {
        return $clientUser->getId() == $documentId;
    }

    protected function verifyCompany(User $clientUser, $documentId)
    {
        $clientCompany = $clientUser->getCompany();
        if(empty($clientCompany)) {
            return false;
        }
        return $clientCompany->getId() == $documentId;
    }

    protected function verifyPresentation(User $clientUser, $documentId)
    {
        $clientCompany = $clientUser->getCompany();
        if(empty($clientCompany)) {
            return false;
        }
        return $clientCompany->getId() == $documentId;
    }

    protected function verifyAppointment(User $clientUser, $documentId)
    {
        /** @var $appointment \Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment */
        $appointment = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Appointment')
            ->findOneById($documentId);

        return $appointment->getUser()->getId() == $clientUser->getId();
    }

}