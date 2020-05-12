# LangSwitch

LangSwitch is a Laravel package that manages language switching. It does this by adding a middleware to all your web routes that uses four different strategies to determine the current locale: checking the hostname, checking a query parameter, checking a cookie, and calling closures.

To customize how your app determines which locale to use, publish this package's config file, which will then be availble as `config/langswitch.php`, and follow the instructions further below.

``` cmd
$ php artisan vendor:publish --provider=CBDCRestigouche\LangSwitch\ServiceProvider
```

It is recommended that you read Laravel's documentation on localization before you read ahead: https://laravel.com/docs/7.x/localization.

## Default Locale

Since Laravel already provides a `fallback_locale` value in `config/app.php`, you can simply change the default value there.

## Strategy Priorities

Using the `strategies` array, you can specify the order in which each strategy is attempted, and by removing them you can even prevent certain strategies from being attempted at all.

For instance, an app that only has one hostname and only uses cookies to determine the locale could be configured as such:

``` php
// bare minimum for the scenario described above
'strategies' => [
	'cookie',
],
```

When you first export the config file, the default order makes some assumptions about what the average user may want. It allows the query parameter to have priority over both the cookie and hostnames so that pages can be shared with the correct language, if desired. It also makes the cookie have priority over hostnames to allow user preferences to be respected.

## Switching by Hostname

Switching locale by hostname is useful if you own a domain names for each of your app's languages and want to handle locale detection on one server. To map each hostname to its desired locale, use the `hostname_locales` array.

Example:

``` php
'hostname_locales' => [
	'website.com' => 'en',
	'siteweb.com' => 'fr',
],
```

## Switching by Query Parameter

Switching locale by query parameter is useful if you only have one hostname, but still want to encode the locale in your app's URL. To configure what query parameter your app uses to determine the locale, change the value of `query_name`.

Example:

``` php
// LangSwitch will now look for 'lang=xx' in query strings
'query_name' => 'lang',
```

## Switching by Cookie

Switching locale by cookie is useful for persisting a selected language between sessions. To configure what cookie name your app uses to determine the locale, change the value of `cookie_name`.

Example:

``` php
// if 'locale' happens to conflict with something
// else in your app, you can change it here
'cookie_name' => 'locale',
```

## Switching by Closure

Finally, switching locale via a closure is the most flexible way to determine the locale used by your app. To specify a closure, simply specify its name in the `strategies` array. The syntax for this is similar to how one would specify routes for a controller, and since this feature is implemented with `app()->call()`, it even supports dependency injection! This closure should either return a valid locale string, or null. **Note that the class in your closure path needs to be bound, either via `$this->app->bind()`, or `$this->app->singleton()`.**

Example:

``` php
'strategies' => [
	'\App\MyClass@myMethod',
	'query',
],
```

A good use case for this strategy is to add a `locale` attribute to your user model and retrieve its value via a controller method. That way, language settings can be synchronized with users!

``` php
// in your config/langswitch.php file
'strategies' => [
	...
	'\App\Http\Controllers\UserController@getLocale',
	...
],
```

``` php
// somewhere inside UserController
public function getLocale() {
	// this assumes you added a 'locale' column to your 'user' table
	return auth()->user()->locale;
}
```

## Facade Utilities

To help you set the application's locale, LangSwitch gives you a facade with three utility methods: `queueLocaleCookie`, `forgetLocaleCookie`, and `getQueryParam`. `queueLocaleCookie` takes a locale string and puts a cookie in the next response that will persist your app's locale. Then, if you later want to forget the locale, simply call `forgetLocaleCookie`. Finally, `getQueryParam` takes a locale string and returns an array that you can use to add query parameters to any route or URL.

``` php
// A good place to use this would be in a controller method
// that redirects back to the previous page
LangSwitch::queueLocaleCookie('en');
```

``` php
// If you ever need to forget the locale cookie
// (for instance, after a login) then you can do it like this
LangSwitch::forgetLocaleCookie();
```

``` php
// You can make URLs with this method that will
// always use the given locale, regardless of the user's
// preferences (as long as query parameters are configured
// to have a higher priority than everything else)
route('some.route', LangSwitch::getQueryParam('en'));
```