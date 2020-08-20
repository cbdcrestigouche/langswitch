<?php

namespace CBDCRestigouche\LangSwitch\Tests;

class FakeController
{
    public function getLocale()
    {
        // Pretend this is grabbing the locale from the currently
        // logged-in user or something ;)
        return 'en';
    }
}
