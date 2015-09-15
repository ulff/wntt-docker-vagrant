<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class AppointmentAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', 'sonata_type_model', ['class' => 'Sysla\WeNeedToTalk\WnttUserBundle\Document\User'])
            ->add('event', 'sonata_type_model', ['class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Event'])
            ->add('presentation', 'sonata_type_model', ['class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation'])
            ->add('isVisited', 'checkbox', [
                'label' => 'Is visited',
                'required' => false,
                'by_reference' => false
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('event')
            ->add('presentation')
            ->add('isVisited')

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
        $datagrid
            ->add('user', null, [
                'label' => 'User',
                'class' => 'Sysla\WeNeedToTalk\WnttUserBundle\Document\User'
            ])
            ->add('event', null, [
                'label' => 'Event',
                'class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Event'
            ])
            ->add('presentation', null, [
                'label' => 'Presentation',
                'class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation'
            ]);
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /** @var $presentation \Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation */
        $presentation = $object->getPresentation();
        /** @var $event \Sysla\WeNeedToTalk\WnttApiBundle\Document\Event */
        $event = $object->getEvent();

        if (!empty($presentation) && !empty($event) && $presentation->getEvent()->getId() != $event->getId()) {
            $errorElement
                ->with('event.id')
                ->addViolation('Event does not match chosen presentation')
                ->end()
            ;
            $errorElement
                ->with('presentation.id')
                ->addViolation('Presentation does not match chosen event')
                ->end()
            ;
        }
    }
}