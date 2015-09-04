<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;

class UserAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', 'text', ['label' => 'Username'])
            ->add('email', 'text', ['label' => 'Email']);

        $user = $this->getSubject();
        $userId = $user->getId();
        if(empty($userId)) {
            $formMapper->add('plainPassword', 'password', ['label' => 'Password']);
        }

        $formMapper->add('phoneNumber', 'text', [
                'label' => 'Phone number',
                'required' => false
            ])
            ->add('roles', 'choice', [
                'choices' => [
                    'ROLE_USER' => 'User',
                    'ROLE_ADMIN' => 'Admin',
                ],
                'multiple' => true,
                'required' => true
            ])
            ->add('company', 'sonata_type_model', [
                'class' => 'Sysla\WeNeedToTalk\WnttApiBundle\Document\Company',
                'required' => false
            ])
            ->add('isContactPerson', 'checkbox', [
                'label' => 'Is contact person',
                'required' => false,
                'by_reference' => false
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
            ->add('company')
            ->add('isContactPerson')

            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [
                        'template' => 'SyslaWeNeedToTalkWnttAdminBundle:CRUD:list__action_delete.html.twig'
                    ],
                ]
            ])
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param User         $object
     *
     * @throws \Exception
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        if(!$object instanceof User) {
            throw new \Exception('Expected object of class Sysla\WeNeedToTalk\WnttUserBundle\Document\User, '.get_class($object).' given');
        }

        $userCompany = $object->getCompany();
        $isContactPerson = $object->getIsContactPerson();
        if(empty($userCompany) && $isContactPerson === true) {
            throw new \Exception('Invalid User state: user not assigned to company cannot be a contact person');
        }
    }

}