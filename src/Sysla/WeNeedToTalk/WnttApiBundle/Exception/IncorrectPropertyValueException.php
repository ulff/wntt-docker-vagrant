<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Exception;

class IncorrectPropertyValueException extends \Exception
{
    public function __construct($propertyName, $propertyExpectedValue, $propertyActualValue)
    {
        $this->message = "JSON '$propertyName' property should have value: '$propertyExpectedValue', actual: '$propertyActualValue'";
    }
}