<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Category;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;

class CategoryListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $object = $eventArgs->getDocument();

        if ($object instanceof Category) {
            /** @var $presentations Presentation[] */
            $presentations = $dm
                ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Presentation')
                ->findBy(['categories.id' => $object->getId()]);

            foreach($presentations as $presentation) {
                $presentation->removeCategory($object);
                $dm->persist($presentation);
            }
            $dm->flush();
        }
    }
}