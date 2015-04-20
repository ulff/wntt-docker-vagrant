<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', 'text', ['label' => 'Username'])
            ->add('email', 'text', ['label' => 'Email'])
            ->add('plainPassword', 'password', ['label' => 'Password'])
            ->add('phoneNumber', 'text', ['label' => 'Phone number'])
            ->add('roles', 'choice', [
                    'choices' => [
                        'ROLE_USER' => 'User',
                        'ROLE_ADMIN' => 'Admin',
                    ],
                    'multiple' => true,
                    'required' => true
                ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('phoneNumber')
            ->add('roles', 'choice', [
                    'multiple' => true,
                    'delimiter' => ' | ',
                    'choices' => [
                        'ROLE_USER'=>'User',
                        'ROLE_ADMIN'=>'Admin',
                        'ROLE_SUPER_ADMIN'=>'Super Admin'
                    ]
                ]
            )

            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [
                        'template' => 'SyslaWeeNeedToTalkWnttAdminBundle:CRUD:list__action_delete.html.twig'
                    ],
                ]
            ])
        ;
    }

}