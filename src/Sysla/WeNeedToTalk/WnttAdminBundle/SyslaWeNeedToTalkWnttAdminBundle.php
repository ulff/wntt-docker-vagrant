<?php

namespace Sysla\WeNeedToTalk\WnttAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SyslaWeNeedToTalkWnttAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataAdminBundle';
    }
}
