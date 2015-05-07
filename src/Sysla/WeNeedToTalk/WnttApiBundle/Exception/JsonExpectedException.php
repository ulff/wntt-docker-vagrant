<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Exception;

class JsonExpectedException extends \Exception
{
    public function __construct()
    {
        $this->message = 'Expected response with type JSON';
    }
}