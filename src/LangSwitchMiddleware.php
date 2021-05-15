<?php

namespace jonathanlafleur\LangSwitch;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LangSwitchMiddleware
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
        app()->setLocale(config('langswitch.default'));

		foreach (config('langswitch.strategies') as $strategy) {
            if ($this->tryStrategy($strategy, $request)) {
                return $next($request);
            }
        }

		return $next($request);
	}

	/**
	 * Tries a given strategy on the current request. Returns true on success, false on failure.
	 *
	 * @param string $strategy
	 * @param \Illuminate\Http\Request $request
	 * @return bool
	 */
	public function tryStrategy(string $strategy, Request $request): bool
	{
		$handlerName = $this->strategyHandlers[$strategy] ?? null;

        if (!is_null($handlerName)) {
			return call_user_func([$this, $handlerName], $request);
        } else {
            try {
                // If a strategy doesn't have a handler, try treating it as a closure name.
                $locale = app()->call($strategy);

				if (!is_null($locale)) {
					app()->setLocale($locale);
					return true;
				}
            } catch (Exception $ex) {
				Log::error($ex);
            }

            return false;
		}
	}

	/**
	 * Switch this website or web app's locale by query parameter.
	 *
	 * @param \Illuminate\Http\Request
	 * @return bool
	 */
	public function switchByQuery(Request $request): bool
	{
		$queryName = config('langswitch.query_name');
		$query = $request->query($queryName);

        if ($query) {
            app()->setLocale($query);
            return true;
        } else {
            return false;
        }
	}

	/**
	 * Switch this website or web app's locale by a cookie.
	 *
	 * @param \Illuminate\Http\Request
	 * @return bool
	 */
	public function switchByCookie(Request $request): bool
	{
		$cookieName = config('langswitch.cookie_name');

		if ($request->hasCookie($cookieName)) {
            app()->setLocale($request->cookie($cookieName));
            return true;
        } else {
            return false;
        }
	}

	/**
	 * Switch this website or web app's locale by the hostname.
	 *
	 * @param \Illuminate\Http\Request
	 * @return bool
	 */
	public function switchByHostname(Request $request): bool
	{
        $hostname = $request->getHost();
		$locale = config('langswitch.hostname_locales')[$hostname] ?? null;

		if (!is_null($locale)) {
            app()->setLocale($locale);
            return true;
        } else {
            return false;
        }
	}
}
