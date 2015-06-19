<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Exception;

class DocumentValidationException extends \Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}