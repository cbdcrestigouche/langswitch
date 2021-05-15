<?php

namespace jonathanlafleur\LangSwitch\Tests;

use jonathanlafleur\LangSwitch\LangSwitchServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
	{
		return [
			LangSwitchServiceProvider::class,
        ];

        $this->app->bind(FakeController::class);
	}
}
