<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;

class CompanyListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $object = $eventArgs->getDocument();

        if ($object instanceof Company) {
            /** @var $users User[] */
            $users = $dm
                ->getRepository('SyslaWeNeedToTalkWnttUserBundle:User')
                ->findBy(['company.id' => $object->getId()]);

            foreach($users as $user) {
                $user->setCompany(null);
                $dm->persist($user);
            }
            $dm->flush();
        }
    }
}