<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CompanyAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'Name'])
            ->add('websiteUrl', 'text', [
                'label' => 'Website URL',
                'required' => false
            ])
            ->add('logoUrl', 'text', [
                'label' => 'Logo URL',
                'required' => false
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'Is enabled',
                'required' => false,
                'by_reference' => false
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('websiteUrl')
            ->add('logoUrl')
            ->add('enabled')

            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }
}