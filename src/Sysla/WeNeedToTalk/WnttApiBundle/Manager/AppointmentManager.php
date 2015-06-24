<?php
namespace Sysla\WeNeedToTalk\WnttApiBundle\Manager;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception\DocumentValidationException;

class AppointmentManager extends AbstractDocumentManager
{
    public function __construct(ManagerRegistry $documentManager)
    {
        $this->documentName = 'Appointment';
        parent::__construct($documentManager);
    }

    protected function createDocumentFromRequest(array $appointmentData)
    {
        $appointment = new Appointment();
        $this->updateDocumentFromRequest($appointment, $appointmentData);

        return $appointment;
    }

    protected function updateDocumentFromRequest(Document $appointment, array $appointmentData)
    {
        /** @var $appointment Appointment */
        $appointment->setIsVisited($appointmentData['isVisited'] == 'true' ? true : false);

        $user = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->findOneById($appointmentData['user']);
        $appointment->setUser($user);

        /** @var $presentation \Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation */
        $presentation = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->findOneById($appointmentData['presentation']);
        $appointment->setPresentation($presentation);

        if(!empty($appointmentData['event'])) {
            $event = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
                ->findOneById($appointmentData['event']);
            $appointment->setEvent($event);
        } else {
            $appointment->setEvent($presentation->getStand()->getEvent());
        }
    }

    protected function validateDocumentData(array $appointmentData, Document $appointment = null)
    {
        if(empty($appointmentData['event'])) {
            return;
        }

        /** @var $event \Sysla\WeNeedToTalk\WnttApiBundle\Document\Event */
        $event = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->findOneById($appointmentData['event']);

        /** @var $presentation \Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation */
        $presentation = $this->documentManager->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->findOneById($appointmentData['presentation']);

        if($event->getId() != $presentation->getStand()->getEvent()->getId()) {
            throw new DocumentValidationException("Presentation '{$presentation->getId()}' does not belong to defined event '{$event->getId()}'");
        }
    }
}