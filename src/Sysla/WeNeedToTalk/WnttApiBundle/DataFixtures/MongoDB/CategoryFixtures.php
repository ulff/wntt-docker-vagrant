<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Category;

class CategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Oil');
        $manager->persist($category);
        $this->addReference('category_oil', $category);

        $category = new Category();
        $category->setName('Petrol');
        $manager->persist($category);
        $this->addReference('category_petrol', $category);

        $category = new Category();
        $category->setName('Gas');
        $manager->persist($category);
        $this->addReference('category_gas', $category);

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}

