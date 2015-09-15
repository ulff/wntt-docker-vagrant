<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;

class PresentationListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $object = $eventArgs->getDocument();
        if ($object instanceof Presentation) {
            /** @var $object Presentation */
            $object->setCompanyName($object->getCompany()->getName());
        }
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $object = $eventArgs->getDocument();
        if ($object instanceof Presentation) {
            /** @var $object Presentation */
            $object->setCompanyName($object->getCompany()->getName());
            $dm->persist($object);
            $dm->flush();
        }
    }
}