<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;

class PresentationManager extends AbstractDocumentManager
{
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'Presentation';
        parent::__construct($documentManager);
    }

    protected function createDocumentFromRequest(array $presentationData)
    {
        $presentation = new Presentation();
        $this->updateDocumentFromRequest($presentation, $presentationData);

        return $presentation;
    }

    protected function updateDocumentFromRequest(Document $presentation, array $presentationData)
    {
        /** @var $presentation Presentation */
        $presentation->setVideoUrl($presentationData['videoUrl']);
        $presentation->setName($presentationData['name']);
        $presentation->setHall($presentationData['hall']);
        $presentation->setNumber($presentationData['number']);
        $presentation->setDescription($presentationData['description']);
        $presentation->setIsPremium($presentationData['isPremium'] == 'true' ? true : false);

        $categories = [];
        $categoryIds = $presentationData['categories'];
        if(is_array($categoryIds)) {
            foreach($categoryIds as $categoryId) {
                $category = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                    ->findOneById($categoryId);
                $categories[] = $category;
            }
        } elseif (!empty($categoryIds)) {
            $category = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Category')
                ->findOneById($presentationData['categories']);
            $categories[] = $category;
        }
        $presentation->setCategories($categories);

        $company = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Company')
            ->findOneById($presentationData['company']);
        $presentation->setCompany($company);

        $event = $this->documentManager->getRepository('SyslaWeNeedToTalkWnttApiBundle:Event')
            ->findOneById($presentationData['event']);
        $presentation->setEvent($event);
    }

    protected function validateDocumentData(array $presentationData, Document $presentation = null)
    {
    }
}