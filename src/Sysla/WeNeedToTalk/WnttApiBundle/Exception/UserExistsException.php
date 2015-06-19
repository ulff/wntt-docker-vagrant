<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Exception;

class UserExistsException extends \Exception
{
    public function __construct()
    {
        $this->message = 'User with given username already exists';
    }
}