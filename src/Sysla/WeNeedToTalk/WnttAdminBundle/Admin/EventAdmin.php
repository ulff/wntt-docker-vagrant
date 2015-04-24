<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class EventAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'Name'])
            ->add('location', 'text', [
                'label' => 'Location',
                'required' => false
            ])
            ->add('dateStart', 'date', ['label' => 'Start date'])
            ->add('dateEnd', 'date', ['label' => 'End date'])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('location')
            ->add('dateStart')
            ->add('dateEnd')

            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }
}