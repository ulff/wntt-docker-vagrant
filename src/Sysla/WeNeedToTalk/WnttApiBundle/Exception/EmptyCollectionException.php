<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Exception;

class EmptyCollectionException extends \Exception
{
    public function __construct($collectionName = null)
    {
        if(!empty($collectionName)) {
            $this->message = "JSON '$collectionName' collection is empty";
        } else {
            $this->message = "JSON collection is empty";
        }
    }
}