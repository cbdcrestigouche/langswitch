<?php

namespace CBDCRestigouche\LangSwitch;

use Illuminate\Support\Facades\Cookie;

class LangSwitch
{
	public function queueLocaleCookie(string $locale)
	{
		Cookie::queue(Cookie::forever(config('langswitch.cookie_name'), $locale));
	}
	
	public function forgetLocaleCookie()
	{
		Cookie::queue(Cookie::forget(config('langswitch.cookie_name')));
	}
	
	public function getQueryParam(string $locale)
	{
		return [config('langswitch.query_name') => $locale];
	}
}