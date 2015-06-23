<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment;

class AppointmentFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $appointment = new Appointment();
        $appointment->setUser($manager->merge($this->getReference('user_freelancer')));
        $appointment->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $appointment->setPresentation($manager->merge($this->getReference('presentation_company_1st_ot_2015')));
        $appointment->setIsVisited(true);
        $manager->persist($appointment);
        $this->addReference('appointment_1', $appointment);

        $appointment = new Appointment();
        $appointment->setUser($manager->merge($this->getReference('user_member')));
        $appointment->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $appointment->setPresentation($manager->merge($this->getReference('presentation_company_1st_ot_2015')));
        $appointment->setIsVisited(false);
        $manager->persist($appointment);
        $this->addReference('appointment_2', $appointment);

        $appointment = new Appointment();
        $appointment->setUser($manager->merge($this->getReference('user_tinyadmin')));
        $appointment->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $appointment->setPresentation($manager->merge($this->getReference('presentation_company_1st_ot_2015')));
        $appointment->setIsVisited(true);
        $manager->persist($appointment);
        $this->addReference('appointment_3', $appointment);

        $appointment = new Appointment();
        $appointment->setUser($manager->merge($this->getReference('user_freelancer')));
        $appointment->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $appointment->setPresentation($manager->merge($this->getReference('presentation_company_2nd_ot_2015')));
        $appointment->setIsVisited(true);
        $manager->persist($appointment);
        $this->addReference('appointment_4', $appointment);

        $appointment = new Appointment();
        $appointment->setUser($manager->merge($this->getReference('user_member')));
        $appointment->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $appointment->setPresentation($manager->merge($this->getReference('presentation_company_2nd_ot_2015')));
        $appointment->setIsVisited(false);
        $manager->persist($appointment);
        $this->addReference('appointment_5', $appointment);

        $appointment = new Appointment();
        $appointment->setUser($manager->merge($this->getReference('user_freelancer')));
        $appointment->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $appointment->setPresentation($manager->merge($this->getReference('presentation_company_2nd_ipe_2014')));
        $appointment->setIsVisited(false);
        $manager->persist($appointment);
        $this->addReference('appointment_6', $appointment);

        $manager->flush();
    }

    public function getOrder() {
        return 4;
    }
}

