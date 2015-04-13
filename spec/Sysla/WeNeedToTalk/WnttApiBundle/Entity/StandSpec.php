<?php

namespace spec\Sysla\WeNeedToTalk\WnttApiBundle\Entity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sysla\WeNeedToTalk\WnttApiBundle\Entity\Stand');
    }
}