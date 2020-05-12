<?php

namespace CBDCRestigouche\LangSwitch;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class Middleware
{
	/**
	 * Maps each strategy to its handler method.
	 * 
	 * @var array
	 */
	private $strategyHandlers = [
		'query'    => 'switchByQuery',
		'cookie'   => 'switchByCookie',
		'hostname' => 'switchByHostname',
	];
	
	/**
	 * Handles requests.
	 */
	public function handle(Request $request, Closure $next)
	{
		foreach (config('langswitch.strategies') as $strategy)
			if ($this->tryStrategy($strategy, $request)) break;
		
		return $next($request);
	}
	
	/**
	 * Tries a given strategy on the current request. Returns true on success, false on failure.
	 * 
	 * @param string $strategy
	 * @param \Illuminate\Http\Request $request
	 * @return bool
	 */
	public function tryStrategy(string $strategy, Request $request)
	{
		$handlerName = $this->strategyHandlers[$strategy] ?? null;
		
		if ($handlerName) {
			return call_user_func([$this, $handlerName], $request);
		} else {
			// If a strategy doesn't have a handler, try treating it as a closure name.
			try {
				$locale = app()->call($strategy);
				if ($locale !== null) {
					app()->setLocale($locale);
					return true;
				}
			} catch (Exception $ex) {
				Log::error($ex);
			}
		}
		
		return false;
	}
	
	/**
	 * Switch this website or web app's locale by query parameter.
	 * 
	 * @param \Illuminate\Http\Request
	 * @return bool
	 */
	public function switchByQuery(Request $request)
	{
		$queryName = config('langswitch.query_name');
		$hasQuery  = $request->has($queryName);
		
		if ($hasQuery) app()->setLocale($request->input($queryName));
		return $hasQuery;
	}
	
	/**
	 * Switch this website or web app's locale by a cookie.
	 * 
	 * @param \Illuminate\Http\Request
	 * @return bool
	 */
	public function switchByCookie(Request $request)
	{
		$cookieName = config('langswitch.cookie_name');
		$hasCookie  = $request->hasCookie($cookieName);
		
		if ($hasCookie) app()->setLocale($request->cookie($cookieName));
		return $hasCookie;
	}
	
	/**
	 * Switch this website or web app's locale by the hostname.
	 * 
	 * @param \Illuminate\Http\Request
	 * @return bool
	 */
	public function switchByHostname(Request $request)
	{
		$hostname = $request->getHost();
		$locale   = config('langswitch.hostname_locales')[$hostname] ?? null;
		
		if ($locale !== null) app()->setLocale($locale);
		return $locale !== null;
	}
	
	/**
	 * Sets this website or web app's locale to the default.
	 */
	public function switchByDefault()
	{
		app()->setLocale(config('langswitch.default'));
	}
}