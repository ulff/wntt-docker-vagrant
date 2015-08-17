<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;

abstract class AbstractWnttRestController extends FOSRestController
{
    protected function createViewWithSerializationContext($includeProperties)
    {
        $view = $this->view();
        $serializerGroups = ['Default'];

        if(!empty($includeProperties)) {
            foreach($includeProperties as $property) {
                $serializerGroups[] = 'incl'.ucfirst($property);
            }
        }
        $view->setSerializationContext(SerializationContext::create()->setGroups($serializerGroups));

        return $view;
    }
}
