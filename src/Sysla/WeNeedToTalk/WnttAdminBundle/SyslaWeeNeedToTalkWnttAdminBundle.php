<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SyslaWeeNeedToTalkWnttAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}