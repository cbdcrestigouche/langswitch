<?php

namespace CBDCRestigouche\LangSwitch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void queueLocaleCookie(string $locale)
 * @method static void forgetLocaleCookie()
 */
class LangSwitch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LangSwitch::class;
    }
}