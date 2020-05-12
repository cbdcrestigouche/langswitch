<?php

namespace CBDCRestigouche\LangSwitch;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
	private $configPath = __DIR__.'/config/langswitch.php';
	
	public function register()
	{
		// merge the user's config file back into our config
		$this->mergeConfigFrom($this->configPath, 'langswitch');
		
		// bind our helper class as a singleton
		$this->app->singleton(LangSwitch::class, function () {
			return new LangSwitch();
		});
	}
	
	public function boot()
	{
		// add our middleware to the group of web middlewares
		$this->app['router']->pushMiddlewareToGroup('web', Middleware::class);
		
		// publish our config file
		$this->publishes([
			$this->configPath => config_path('langswitch.php'),
		]);
	}
}