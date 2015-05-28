<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;

class PresentationRepository extends DocumentRepository
{
    public function findByEvent($eventId)
    {
        $eventStands = $this
            ->getDocumentManager()
            ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->findBy(['event.id' => $eventId]);

        $standIds = [];
        foreach($eventStands as $es) {
            $standIds[] = $es->getId();
        }

        $qb = $this->createQueryBuilder();
        $qb->field('stand.id')->in($standIds);

        $resultArray = [];
        foreach($qb->getQuery()->execute()->toArray() as $presentation) {
            $resultArray[] = $presentation;
        }

        return $resultArray;
    }
}