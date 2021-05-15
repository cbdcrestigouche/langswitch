<?php

namespace jonathanlafleur\LangSwitch\Tests\Unit;

use jonathanlafleur\LangSwitch\LangSwitchMiddleware;
use jonathanlafleur\LangSwitch\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LangSwitchMiddlewareTest extends TestCase
{
    protected function assertLocale(Request $request, string $locale)
    {
        (new LangSwitchMiddleware())->handle($request, function($request) use ($locale) {
            $this->assertEquals($locale, app()->getLocale());
        });
    }

    /** @test */
    public function defaultLocaleIsUsed()
    {
        // mock config
        Config::set('langswitch.default', 'fr');

        $request = new Request();
        $this->assertLocale($request, 'fr');
    }

    /** @test */
    public function canSwitchByHostname()
    {
        // mock config
        Config::set('langswitch.default', 'fr');
        Config::set('langswitch.hostname_locales', [
            'fr.domain.invalid' => 'fr',
            'en.domain.invalid' => 'en',
        ]);
        Config::set('langswitch.strategies', ['hostname']);

        // set to english hostname
        $request = new Request();
        $request->headers->set('HOST', 'en.domain.invalid');
        $this->assertLocale($request, 'en');

        // set to french hostname
        $request = new Request();
        $request->headers->set('HOST', 'fr.domain.invalid');
        $this->assertLocale($request, 'fr');
    }

    /** @test */
    public function canSwitchByQueryParam()
    {
        // mock config
        Config::set('langswitch.default', 'jp');
        Config::set('langswitch.query_name', 'lang');
        Config::set('langswitch.strategies', ['query']);

        // request english site
        $request = new Request();
        $request->query->add(['lang' => 'en']);
        $this->assertLocale($request, 'en');

        // request french site
        $request = new Request();
        $request->query->add(['lang' => 'fr']);
        $this->assertLocale($request, 'fr');
    }

    /** @test */
    public function canSwitchByCookie()
    {
        // mock config
        Config::set('langswitch.default', 'jp');
        Config::set('langswitch.cookie_name', 'lang');
        Config::set('langswitch.strategies', ['cookie']);

        // request english site
        $request = new Request();
        $request->cookies->add(['lang' => 'en']);
        $this->assertLocale($request, 'en');

        // request french site
        $request = new Request();
        $request->cookies->add(['lang' => 'fr']);
        $this->assertLocale($request, 'fr');
    }

    /** @test */
    public function canSwitchByClosure()
    {
        // mock config
        Config::set('langswitch.default', 'jp');
        Config::set('langswitch.strategies', [
            '\jonathanlafleur\LangSwitch\Tests\FakeController@getLocale'
        ]);

        // FakeController is bound in our base TestCase via
        // $this->app->bind(FakeController::class);

        // assert that FakeController@getLocale will return 'en'
        $this->assertLocale(new Request(), 'en');
    }
}
