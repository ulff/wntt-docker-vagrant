<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;

class CompanyFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $company = new Company();
        $company->setName('Company 1st');
        $company->setWebsiteUrl('http://company1.com');
        $company->setLogoUrl('http://company1.com/logo');
        $manager->persist($company);
        $this->addReference('company_1st', $company);

        $company = new Company();
        $company->setName('Company 2nd');
        $company->setWebsiteUrl('http://company2.com');
        $manager->persist($company);
        $this->addReference('company_2nd', $company);

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}

