<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;


class StandAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('event', 'sonata_type_model', ['class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Event'])
            ->add('hall', 'text', ['label' => 'Hall'])
            ->add('number', 'text', ['label' => 'Number'])
            ->add('company', 'sonata_type_model', [
                'class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Company',
                'required' => false
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('event')
            ->add('hall')
            ->add('number')
            ->add('company')

            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid->add('event', null, [
            'label' => 'Event',
            'class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Event'
        ]);
    }
}