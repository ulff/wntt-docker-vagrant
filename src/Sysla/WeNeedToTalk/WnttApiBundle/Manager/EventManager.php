<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Event;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;

class EventManager extends AbstractDocumentManager
{
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'Event';
        parent::__construct($documentManager);
    }

    protected function createDocumentFromRequest(array $eventData)
    {
        $event = new Event();
        $this->updateDocumentFromRequest($event, $eventData);

        return $event;
    }

    protected function updateDocumentFromRequest(Document $event, array $eventData)
    {
        /** @var $event Event */
        $event->setName($eventData['name']);
        $event->setLocation($eventData['location']);
        $event->setDateStart(new \DateTime($eventData['dateStart']));
        $event->setDateEnd(new \DateTime($eventData['dateEnd']));
    }
}