# WP Blade(One)

Wordpress plugin to allow the use of Blade syntax in your templates.

to achieve this, this plugin use [BladeOne](https://github.com/EFTEC/BladeOne/), which is a standalone port of Laravel's Blade, without dependencies.

## 1 - Installation

You will need to use composer and have [composer/installers](https://github.com/composer/installers) installed and configured, so composer knows how to install this plugin in the right wordpress directory.

Then you just need to

`composer require dievardump/wp-bladeone-plugin`

And activate it in the Wordpress plugins panel

## 2 - Configuration

There are 3 constants that you can define to configure the WP BladeOne.

- `WP_BLADEONE_VIEWS`

Where BladeOne will look for the view files.
If you use [WP Blade(One) Starter Template](https://github.com/dievardump/wp-bladeone-theme) or want `WP BladeOne` to work with Wordpress hierarchy, it should be your theme directory.
If you just want to render some templates using `wp_bladeone()->run($view_name, $data)` then you can set another path. (see Usage below)
default to `get_stylesheet_directory()`

- `WP_BLADEONE_CACHE`

Where BladeOne will have the right to write the views cached files. Usually somewhere in `WP_CONTENT_DIR` as other directories are often not writable.
default to `WP_CONTENT_DIR . '/cache/.wp-bladeone-cache'`


- `WP_BLADEONE_MODE`

Configures how BladeOne manages the rendering of the views, including caching.
See [BladeOne](https://github.com/EFTEC/BladeOne/) to know what the different modes are and what they do.
default to `\eftec\bladeone\BladeOne::MODE_AUTO`

## 3 - Usage

There are two ways to use this Plugin in your templates.

### 3.1 Using Hierarchy

This plugin will hook on some Wordpress actions so when Wordpress tries to render a template file, it will first look if a Blade version is available. (i.e: before rendering `index.php` it will first look if `index.blade.php` exists).

This allows you to create full themes working entirely with Blade syntax (see [WP Blade(One) Starter Template](https://github.com/dievardump/wp-bladeone-theme) as en example)


### 3.2 Using `wp_bladeone()->run($view_name, $data)`

If you don't want to use the hierarchical hooks, you can set `WP_BLADEONE_VIEWS` to `/views` (or any other directory for that matters) and put all your blade template files there.

Then, in your themes files, when you want to use a Blade view just do `wp_bladeone()->run($view_name, $data)`.

Exemple:
file `wp-config.php`
```php
<?php
// wp configuration
define('WP_BLADEONE_VIEWS', __DIR__ . '/../views');
//....
?>
```
In your theme directory :

file `index.php`
```php
<?php
// this will look for WP_BLADEONE_VIEWS . '/index.blade.php'
// => () . '/views/index.blade.php'
echo wp_bladeone()->run('index', ['className' => 'index']);
?>
```

Where you put your views

file `views/index.blade.php`
```
@extends('layout')

@section('title')
<h1>{{ get_the_title() }}</h1>
@endsection

@section('content')
	@php the_content() @endphp
@endsection
```

file `views/layout.blade.php`
```php
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
		<article class="{{ $className ?? ''}}">
		@section('title')
			<h1>Default title</h1>
		@show
		@section('content')
			<p>default content</p>
		@show
		</article>
</body>
</html>
```

And voilà !

## 4 - How does it actually work?

When deciding what template file need to be required to display a page/post, Wordpress goes through a [Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/), taking the first available template.
Like for a lot of things, Wordpress provides actions for developers to modify the core behavior of this Template Hierarchy, and this plugin only modifies the behavior by prioritizing files ending with `.blade.php` over `.php`

For example, let's say you try to load a normal page with the slug `/example/` and id `10`
Wordpress will look in order for
```php
[
	'page-exemple.php',
	'page-10.php',
	'page.php',
	'singular.php',
	'index.php'
]
```
This plugin will just modify this so wordpress will look for
```php
[
	'page-exemple.blade.php',
	'page-exemple.php',
	'page-10.blade.php',
	'page-10.php',
	'page.blade.php',
	'page.php',
	'singular.blade.php',
	'singular.php',
	'index.blade.php',
	'index.php'
]
```

Then, if the first existing file ends with `.blade.php` one, it will just be rendered through BladeOne.

## 5 - Why not use roots' Sage?

I just wish to be able to use Blade's syntax, nothing else. Sage brings a lot of things that I have absolutely no use of since my stack is totally different than theirs.

However, I actually use this plugin with Bedrock (by the same creators of Sage) and my own [WP Blade(One) Starter Template](https://github.com/dievardump/wp-bladeone-theme), which I started from the template hierarchy of Sage.