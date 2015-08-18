<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sysla\WeNeedToTalk\WnttOAuthBundle\Document\Client;

class ClientAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text', ['label' => 'Client name']);
        $formMapper->add('clientId', 'text', [
            'label' => 'Client ID',
            'read_only' => true,
            'disabled'  => true
        ]);
        $formMapper->add('secret', 'text', [
            'label' => 'Client secret',
            'read_only' => true,
            'disabled'  => true
        ]);
        $formMapper->add('allowedGrantTypes', 'choice', [
            'choices' => [
                'client_credentials' => 'client_credentials',
                'password' => 'password',
                'refresh_token' => 'refresh_token'
            ],
            'data' => [
                'client_credentials' => 'client_credentials',
                'password' => 'password',
                'refresh_token' => 'refresh_token'
            ],
            'multiple' => true,
            'required' => true,
            'label' => 'Allowed grant types'
        ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('clientId', 'text', ['label' => 'Client ID'])
            ->add('secret', 'text', ['label' => 'Client secret'])
            ->add('allowedGrantTypes', 'choice', [
                    'multiple' => true,
                    'delimiter' => ' | ',
                    'label' => 'Allowed grant types',
                    'choices' => [
                        'client_credentials' => 'client_credentials',
                        'password' => 'password',
                        'refresh_token' => 'refresh_token'
                    ]
                ]
            )

            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ])
        ;
    }

}