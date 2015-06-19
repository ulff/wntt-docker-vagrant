<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;

class StandManager extends AbstractDocumentManager
{
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'Stand';
        parent::__construct($documentManager);
    }

    protected function createDocumentFromRequest(array $standData)
    {
        $stand = new Stand();
        $this->updateDocumentFromRequest($stand, $standData);

        return $stand;
    }

    protected function updateDocumentFromRequest(Document $stand, array $standData)
    {
        /** @var $stand Stand */
        $stand->setNumber($standData['number']);
        $stand->setHall($standData['hall']);

        $company = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->findOneById($standData['company']);
        $stand->setCompany($company);

        $event = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->findOneById($standData['event']);
        $stand->setEvent($event);
    }

    protected function validateDocumentData(array $standData, Document $stand = null)
    {
    }
}