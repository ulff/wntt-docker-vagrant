<?php

namespace spec\Sysla\WeNeedToTalk\WnttApiBundle\Document;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sysla\WeNeedToTalk\WnttApiBundle\Document\Event');
    }
}