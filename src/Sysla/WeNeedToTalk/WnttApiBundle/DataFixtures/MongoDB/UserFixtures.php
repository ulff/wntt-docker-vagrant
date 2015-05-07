<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('tinyadmin');
        $user->setEmail('tinyadmin@wntt');
        $user->setPlainPassword('password');
        $user->setEnabled(true);
        $user->setRoles([
            'ROLE_USER',
            'ROLE_ADMIN'
        ]);
        $manager->persist($user);
        $this->addReference('user_tinyadmin', $user);

        $user = $userManager->createUser();
        $user->setUsername('freelancer');
        $user->setEmail('freelancer@nowhere');
        $user->setPlainPassword('password');
        $user->setEnabled(true);
        $manager->persist($user);
        $this->addReference('user_freelancer', $user);

        $user = $userManager->createUser();
        $user->setUsername('company_member');
        $user->setEmail('member@company');
        $user->setPlainPassword('password');
        $user->setEnabled(true);
        $manager->persist($user);
        $this->addReference('user_member', $user);

        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }
}

