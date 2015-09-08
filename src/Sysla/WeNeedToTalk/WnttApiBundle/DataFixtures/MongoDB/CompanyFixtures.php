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
        $company->setName('Schibsted Media Group');
        $company->setWebsiteUrl('http://schibsted.com');
        $company->setLogoUrl('http://www.schibsted.com/Global/LogoTypes/Logos%202014/SMG_Large_2014_RGB.jpg');
        $manager->persist($company);
        $this->addReference('company_2nd', $company);

        $company = new Company();
        $company->setName('Company Disabled');
        $company->setWebsiteUrl('http://disabled.io');
        $company->setLogoUrl('https://cdn2.iconfinder.com/data/icons/interface-part-1/32/circle-diagonal-line-512.png');
        $company->setEnabled(false);
        $manager->persist($company);
        $this->addReference('company_disabled', $company);

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}

