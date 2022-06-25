# BelongsTo field with dependency support for Nova

This package is an extension of Laravel Nova's existing BelongsTo field and Vue components.

## Requirements

- PHP 7.3+
- Nova 4+

## Installation

You can install this package on a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require aqjw/nova-belongs-to-dependency
```

## Usage

The following will list categories with `type_id` equal to the value set in the first BelongsTo field.

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    BelongsTo::make('Type'),
    
    BelongsToDependency::make('User')
        ->dependsOn('type', 'type_id'), // available with one parameter: ->dependsOn('type'),
    ...
];
```

Look at the [example](#example) below for other use cases.

## Example

Database Structure

- Type (id, name)
- Posts (id, type_id, category_id, title, body)
- Category (id, type_id, title)

We should only be able to assign categories to posts that belong to the same type.

This is how you would achieve it on the Nova category resource:

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    BelongsTo::make('Type'),
    
    BelongsToDependency::make('User')
        ->dependsOn('type', 'type_id'),
    ...
];
```

This would work if you used a text/enum `type` field too.

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    Select::make('Type')->options([
        'post' => 'Post',
        'page' => 'Page',
    ])->displayUsingLabels(),
    
    BelongsToDependency::make('User')
        ->dependsOn('type', 'type'),
    ...
];
```

The `searchable` method is also available. Use it if you have tons of entries.

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    BelongsTo::make('Country')
        ->searchable(),
    
    BelongsToDependency::make('State')
        ->searchable()
        ->dependsOn('country'),

    BelongsToDependency::make('City')
        ->searchable()
        ->dependsOn('state'),
    ...
];
```

## License

The MIT License (MIT). Please see License File for more information.
