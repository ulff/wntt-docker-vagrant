<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Exception;

class CollectionExpectedException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Expected response JSON with collection';
    }
}