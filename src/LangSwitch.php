<?php

namespace CBDCRestigouche\LangSwitch;

use Illuminate\Support\Facades\Cookie;

class LangSwitch
{
	public function queueLocaleCookie(string $locale): void
	{
		Cookie::queue(Cookie::forever(config('langswitch.cookie_name'), $locale));
	}
	
	public function forgetLocaleCookie(): void
	{
		Cookie::queue(Cookie::forget(config('langswitch.cookie_name')));
	}
}