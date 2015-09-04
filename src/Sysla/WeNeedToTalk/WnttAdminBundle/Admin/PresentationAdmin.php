<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;


class PresentationAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('videoUrl', 'text', [
                'label' => 'Video URL',
                'required' => true
            ])
            ->add('name', 'text', [
                'label' => 'Name',
                'required' => true
            ])
            ->add('description', 'text', [
                'label' => 'Description',
                'required' => false
            ])
            ->add('hall', 'text', [
                'label' => 'Hall',
                'required' => false
            ])
            ->add('number', 'text', [
                'label' => 'Number',
                'required' => false
            ])
            ->add('company', 'sonata_type_model', ['class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Company'])
            ->add('event', 'sonata_type_model', ['class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Event'])
            ->add('categories', 'sonata_type_model', [
                'class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Category',
                'multiple' => true,
                'required' => false
            ])
            ->add('isPremium', 'checkbox', [
                'label' => 'Is premium',
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
            ->add('company')
            ->add('event')
            ->add('hall')
            ->add('number')
            ->add('categories')
            ->add('isPremium')

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
        $datagrid->add('isPremium', null, [
            'label' => 'Is premium'
        ]);
    }
}