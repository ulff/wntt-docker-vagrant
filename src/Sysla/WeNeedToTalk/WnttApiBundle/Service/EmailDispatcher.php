<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Service;

use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Company;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailDispatcher
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sendUserConfirmationEmail(User $user)
    {
        $activationLink = $this->container->get('router')->generate(
            'wntt_registration_confirm',
            array('token' => $user->getConfirmationToken()),
            true
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('You\'ve just registered to We Need To Talk')
            ->setFrom($this->container->getParameter('wntt.email.from'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->container->get('templating')->render('Email/registration.html.twig', [
                    'username' => $user->getUsername(),
                    'activationLink' => $activationLink,
                ]),
                'text/html'
            )
            ->addPart(
                $this->container->get('templating')->render('Email/registration.txt.twig', [
                    'username' => $user->getUsername(),
                    'activationLink' => $activationLink,
                ]),
                'text/plain'
            )
        ;

        $this->container->get('mailer')->send($message);
    }

    public function sendNewCompanyRegsiteredNotification(Company $company)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('New company registered in WNTT')
            ->setFrom($this->container->getParameter('wntt.email.from'))
            ->setTo($this->container->getParameter('wntt.email.admin'))
            ->setBody(
                $this->container->get('templating')->render('Email/new_company.html.twig', ['companyname' => $company->getName()]),
                'text/html'
            )
            ->addPart(
                $this->container->get('templating')->render('Email/new_company.txt.twig', ['companyname' => $company->getName()]),
                'text/plain'
            )
        ;

        $this->container->get('mailer')->send($message);
    }
}