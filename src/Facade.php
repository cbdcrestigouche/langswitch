<?php

namespace CBDCRestigouche\LangSwitch;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * @method static void queueLocaleCookie(string $locale)
 * @method static array getQueryParam(string $locale)
 */
class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return LangSwitch::class;
    }
}