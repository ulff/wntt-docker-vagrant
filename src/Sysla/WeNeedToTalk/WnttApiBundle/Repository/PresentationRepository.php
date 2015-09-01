<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;

class PresentationRepository extends DocumentRepository
{
    public function findBySearchParams($searchParams, $eventId = null)
    {
        $qb = $this->createQueryBuilder();

        if(!empty($eventId)) {
            $eventStands = $this
                ->getDocumentManager()
                ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
                ->findBy(['event.id' => $eventId]);

            $standIds = [];
            foreach($eventStands as $es) {
                $standIds[] = $es->getId();
            }

            $qb->field('stand.id')->in($standIds);
        }

        if(!empty($searchParams['name'])) {
            $qb->field('name')->equals(new \MongoRegex('/.*'.$searchParams['name'].'.*/i'));
        }
        if(!empty($searchParams['description'])) {
            $qb->field('description')->equals(new \MongoRegex('/.*'.$searchParams['description'].'.*/i'));
        }

        if(!empty($searchParams['company.name'])) {
            $matchingCompanies = $this
                ->getDocumentManager()
                ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
                ->findBy(['name' => new \MongoRegex('/.*'.$searchParams['company.name'].'.*/i')]);
            $companyIds = [];
            foreach($matchingCompanies as $item) {
                $companyIds[] = $item->getId();
            }

            $qb->field('company.id')->in($companyIds);
        }

        if(!empty($searchParams['category.name'])) {
            $matchingCateories = $this
                ->getDocumentManager()
                ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
                ->findBy(['name' => new \MongoRegex('/.*'.$searchParams['category.name'].'.*/i')]);
            $categoryIds = [];
            foreach($matchingCateories as $item) {
                $categoryIds[] = $item->getId();
            }

            $qb->field('categories.id')->in($categoryIds);
        }

        if(!empty($searchParams['stand.hall']) || !empty($searchParams['stand.number'])) {
            $standParams = [];
            if(!empty($searchParams['stand.hall'])) {
                $standParams['hall'] = $searchParams['stand.hall'];
            }
            if(!empty($searchParams['stand.number'])) {
                $standParams['number'] = $searchParams['stand.number'];
            }

            $matchingStands = $this
                ->getDocumentManager()
                ->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
                ->findBy($standParams);
            $standIds = [];
            foreach($matchingStands as $item) {
                $standIds[] = $item->getId();
            }

            $qb->field('stand.id')->in($standIds);
        }

        $resultArray = [];
        foreach($qb->getQuery()->execute()->toArray() as $presentation) {
            $resultArray[] = $presentation;
        }

        return $resultArray;
    }
}