<?php

namespace CBDCRestigouche\LangSwitch;

use Illuminate\Support\ServiceProvider;

class LangSwitchServiceProvider extends ServiceProvider
{
	public function register()
	{
		// merge the user's config file back into our config
		$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'langswitch');

		// bind our helper class as a singleton
		$this->app->singleton(LangSwitch::class, function () {
			return new LangSwitch();
		});
	}

	public function boot()
	{
		// add our middleware to the group of web middlewares
		$this->app['router']->pushMiddlewareToGroup('web', LangSwitchMiddleware::class);

		// publish our config file
		$this->publishes([
			__DIR__.'/../config/config.php' => config_path('langswitch.php'),
		]);
	}
}
