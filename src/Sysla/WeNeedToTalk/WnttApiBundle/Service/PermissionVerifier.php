<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Service;

use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;

class PermissionVerifier
{
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

}