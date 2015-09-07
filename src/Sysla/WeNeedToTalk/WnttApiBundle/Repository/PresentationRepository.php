<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;

class PresentationRepository extends DocumentRepository
{
    public function findBySearchParams($searchParams, $eventId = null, $companyId = null, $sorting = [])
    {
        $qb = $this->createQueryBuilder();

        if(!empty($eventId)) {
            $qb->field('event.id')->equals($eventId);
        }
        if(!empty($companyId)) {
            $qb->field('company.id')->equals($companyId);
        }

        if(!empty($searchParams['name'])) {
            $qb->field('name')->equals(new \MongoRegex('/.*'.$searchParams['name'].'.*/i'));
        }
        if(!empty($searchParams['hall'])) {
            $qb->field('hall')->equals($searchParams['hall']);
        }
        if(!empty($searchParams['number'])) {
            $qb->field('number')->equals($searchParams['number']);
        }
        if(!empty($searchParams['description'])) {
            $qb->field('description')->equals(new \MongoRegex('/.*'.$searchParams['description'].'.*/i'));
        }

        if(!empty($searchParams['company.name'])) {
            $matchingCompanies = $this
                ->getDocumentManager()
                ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
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
                ->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                ->findBy(['name' => new \MongoRegex('/.*'.$searchParams['category.name'].'.*/i')]);
            $categoryIds = [];
            foreach($matchingCateories as $item) {
                $categoryIds[] = $item->getId();
            }

            $qb->field('categories.id')->in($categoryIds);
        }

        if(!empty($sorting['sortby'])) {
            $sortdir = empty($sorting['sortdir']) ? 'asc' : $sorting['sortdir'];
            $qb->sort($sorting['sortby'], $sortdir);
        }

        $resultArray = [];
        foreach($qb->getQuery()->execute()->toArray() as $presentation) {
            $resultArray[] = $presentation;
        }

        return $resultArray;
    }
}