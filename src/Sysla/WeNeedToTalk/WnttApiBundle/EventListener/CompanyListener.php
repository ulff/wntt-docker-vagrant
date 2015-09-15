<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;

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

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $object = $eventArgs->getDocument();

        if ($object instanceof Company) {
            /** @var $presentations Presentation[] */
            $presentations = $dm
                ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Presentation')
                ->findBy(['company.id' => $object->getId()]);

            foreach($presentations as $presentation) {
                $presentation->setCompanyName($object->getName());
                $dm->persist($presentation);
            }
            $dm->flush();
        }
    }
}