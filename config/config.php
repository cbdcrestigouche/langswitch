<?php

return [
	/*
    |--------------------------------------------------------------------------
    | Default locale
    |--------------------------------------------------------------------------
    |
	| The locale to use as a fallback when no strategy can find one.
    |
	*/
	
	'default' => 'en',
	
	/*
    |--------------------------------------------------------------------------
    | Hostname locales
    |--------------------------------------------------------------------------
    |
    | Map each of your website's hostnames to a locale.
    |
	*/
	
	'hostname_locales' => [
		// 'website.com' => 'en'
	],
	
	/*
    |--------------------------------------------------------------------------
    | Query parameter name
    |--------------------------------------------------------------------------
    |
    | The name of the query parameter to use to detect the locale.
    |
	*/
	
	'query_name' => 'locale',
	
	/*
    |--------------------------------------------------------------------------
    | Cookie name
    |--------------------------------------------------------------------------
    |
    | The name of the cookie to use to detect the locale.
    |
	*/
	
	'cookie_name' => 'locale',
	
	/*
    |--------------------------------------------------------------------------
    | Strategies & priorities
    |--------------------------------------------------------------------------
    |
	| Locale detection strategies in the order that they should be
	| attempted. If a strategy succeeds in finding a locale,
	| subsequent ones aren't attempted.
	| 
	| Possible values: 'query', 'cookie', and 'hostname'.
	| Closures are accepted as well.
    |
	*/
	
	'strategies' => [
		'query',
		// '\App\MyClass@myMethod', // replace this with your own closure
		'cookie',
		'hostname',
	],
];