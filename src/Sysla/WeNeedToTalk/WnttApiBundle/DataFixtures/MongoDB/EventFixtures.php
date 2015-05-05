<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Event;

class EventFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $event = new Event();
        $event->setName('International Petrol Exhibition');
        $event->setLocation('Oslo');
        $event->setDateStart(new \DateTime('2014-04-01'));
        $event->setDateEnd(new \DateTime('2014-04-05'));
        $manager->persist($event);
        $this->addReference('event_ipe_2014', $event);

        $event = new Event();
        $event->setName('International Petrol Exhibition');
        $event->setLocation('Oslo');
        $event->setDateStart(new \DateTime('2015-04-02'));
        $event->setDateEnd(new \DateTime('2015-04-06'));
        $manager->persist($event);
        $this->addReference('event_ipe_2015', $event);

        $event = new Event();
        $event->setName('Oil Trade 2014');
        $event->setLocation('Bergen');
        $event->setDateStart(new \DateTime('2014-10-01'));
        $event->setDateEnd(new \DateTime('2014-10-02'));
        $manager->persist($event);
        $this->addReference('event_ot_2014', $event);

        $event = new Event();
        $event->setName('Oil Trade 2015');
        $event->setLocation('Bergen');
        $event->setDateStart(new \DateTime('2015-10-06'));
        $event->setDateEnd(new \DateTime('2015-10-07'));
        $manager->persist($event);
        $this->addReference('event_ot_2015', $event);

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}