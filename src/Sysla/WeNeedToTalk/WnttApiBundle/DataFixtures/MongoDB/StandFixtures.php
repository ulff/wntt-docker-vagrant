<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand;

class StandFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->load_ipe_2014($manager);
        $this->load_ipe_2015($manager);
        $this->load_ot_2014($manager);
        $this->load_ot_2015($manager);

        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }

    private function load_ipe_2014(ObjectManager $manager)
    {
        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $stand->setHall('A');
        $stand->setNumber('A1');
        $stand->setCompany($manager->merge($this->getReference('company_1st')));
        $manager->persist($stand);
        $this->addReference('stand_ipe_2014_A_A1', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $stand->setHall('A');
        $stand->setNumber('A2');
        $stand->setCompany($manager->merge($this->getReference('company_2nd')));
        $manager->persist($stand);
        $this->addReference('stand_ipe_2014_A_A2', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $stand->setHall('A');
        $stand->setNumber('A3');
        $manager->persist($stand);
        $this->addReference('stand_ipe_2014_A_A3', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $stand->setHall('A');
        $stand->setNumber('A4');
        $manager->persist($stand);
        $this->addReference('stand_ipe_2014_A_A4', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $stand->setHall('B');
        $stand->setNumber('B1');
        $manager->persist($stand);
        $this->addReference('stand_ipe_2014_B_B1', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $stand->setHall('B');
        $stand->setNumber('B2');
        $manager->persist($stand);
        $this->addReference('stand_ipe_2014_B_B2', $stand);
    }

    private function load_ipe_2015(ObjectManager $manager)
    {
        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2015')));
        $stand->setHall('A');
        $stand->setNumber('A1');
        $manager->persist($stand);
        $this->addReference('stand_ipe_2015_A_A1', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2015')));
        $stand->setHall('A');
        $stand->setNumber('A2');
        $manager->persist($stand);
        $this->addReference('stand_ipe_2015_A_A2', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ipe_2015')));
        $stand->setHall('A');
        $stand->setNumber('A3');
        $stand->setCompany($manager->merge($this->getReference('company_1st')));
        $manager->persist($stand);
        $this->addReference('stand_ipe_2015_A_A3', $stand);
    }

    private function load_ot_2014(ObjectManager $manager)
    {
        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2014')));
        $stand->setNumber(1);
        $manager->persist($stand);
        $this->addReference('stand_ot_2014_1', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2014')));
        $stand->setNumber(2);
        $manager->persist($stand);
        $this->addReference('stand_ot_2014_2', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2014')));
        $stand->setNumber(3);
        $stand->setCompany($manager->merge($this->getReference('company_1st')));
        $manager->persist($stand);
        $this->addReference('stand_ot_2014_3', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2014')));
        $stand->setNumber(4);
        $manager->persist($stand);
        $this->addReference('stand_ot_2014_4', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2014')));
        $stand->setNumber(5);
        $manager->persist($stand);
        $this->addReference('stand_ot_2014_5', $stand);
    }

    private function load_ot_2015(ObjectManager $manager)
    {
        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $stand->setNumber(1);
        $stand->setCompany($manager->merge($this->getReference('company_2nd')));
        $manager->persist($stand);
        $this->addReference('stand_ot_2015_1', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $stand->setNumber(2);
        $manager->persist($stand);
        $this->addReference('stand_ot_2015_2', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $stand->setNumber(3);
        $manager->persist($stand);
        $this->addReference('stand_ot_2015_3', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $stand->setNumber(4);
        $manager->persist($stand);
        $this->addReference('stand_ot_2015_4', $stand);

        $stand = new Stand();
        $stand->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $stand->setNumber(5);
        $stand->setCompany($manager->merge($this->getReference('company_1st')));
        $manager->persist($stand);
        $this->addReference('stand_ot_2015_5', $stand);
    }
}

