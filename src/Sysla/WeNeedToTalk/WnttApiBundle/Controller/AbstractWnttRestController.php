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

    protected function verifyDocumentExists($documentId, $documentName, $bundleName = 'SyslaWeNeedToTalkWnttApiBundle')
    {
        $document = $this->get('doctrine_mongodb')
            ->getRepository($bundleName.':'.$documentName)
            ->find($documentId);

        if (empty($document)) {
            throw $this->createNotFoundException('No '.$documentName.' found for id '.$documentId);
        }

        return $document;
    }
}
