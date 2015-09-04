<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation;

class PresentationFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $presentation = new Presentation();
        $presentation->setVideoUrl('http://thumbs.dreamstime.com/z/businessman-doing-presentation-clipart-picture-male-cartoon-character-35916626.jpg');
        $presentation->setHall('X');
        $presentation->setNumber('5');
        $presentation->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $presentation->setCompany($manager->merge($this->getReference('company_1st')));
        $presentation->setName('Company 1st, Oil Trade 2015');
        $presentation->setDescription('Presenation of Company 1st on Oil Trade 2015');
        $presentation->setCategories([
            $manager->merge($this->getReference('category_oil')),
            $manager->merge($this->getReference('category_gas'))
        ]);
        $presentation->setIsPremium(true);
        $manager->persist($presentation);
        $this->addReference('presentation_company_1st_ot_2015', $presentation);

        $presentation = new Presentation();
        $presentation->setVideoUrl('http://company2/performance.pdf');
        $presentation->setHall('A');
        $presentation->setNumber('A2');
        $presentation->setEvent($manager->merge($this->getReference('event_ipe_2014')));
        $presentation->setCompany($manager->merge($this->getReference('company_2nd')));
        $presentation->setName('Company 2nd, International Petrol Exhibition 2014');
        $presentation->setDescription('Presenation of Company 2nd on International Petrol Exhibition 2014');
        $presentation->setCategories([
            $manager->merge($this->getReference('category_petrol'))
        ]);
        $manager->persist($presentation);
        $this->addReference('presentation_company_2nd_ipe_2014', $presentation);

        $presentation = new Presentation();
        $presentation->setVideoUrl('http://garrreynolds.com/wordpress/wp-content/uploads/2013/06/3-deliver.jpg');
        $presentation->setHall('X');
        $presentation->setNumber('3');
        $presentation->setEvent($manager->merge($this->getReference('event_ot_2015')));
        $presentation->setCompany($manager->merge($this->getReference('company_2nd')));
        $presentation->setName('Company 2nd, Oil Trade 2015');
        $presentation->setDescription('Presenation of Company 2nd on Oil Trade 2015');
        $presentation->setCategories([
            $manager->merge($this->getReference('category_petrol')),
            $manager->merge($this->getReference('category_oil'))
        ]);
        $manager->persist($presentation);
        $this->addReference('presentation_company_2nd_ot_2015', $presentation);

        for($i=1; $i<=20; $i++) {
            $presentation = new Presentation();
            $presentation->setVideoUrl('http://multipresentations/'.$i);
            $presentation->setHall('B');
            $presentation->setNumber($i);
            $presentation->setEvent($manager->merge($this->getReference('event_mo_i_rana')));
            $presentation->setCompany($manager->merge($this->getReference('company_2nd')));
            $presentation->setName('Presentation '.$i);
            $manager->persist($presentation);
            $this->addReference('presentation_'.$i, $presentation);
        }

        $manager->flush();
    }

    public function getOrder() {
        return 3;
    }
}

