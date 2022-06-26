# BelongsTo field with dependency support for Nova

This package is an extension of Laravel Nova's existing BelongsTo field and Vue components.

## Requirements

- PHP 8.0.2+
- Nova 4+

## Installation

You can install this package on a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require aqjw/belongs-to-dependency
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


If you want to add your own conditions to the query, use the `buildQuery` method.

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    BelongsToDependency::make('Product')
        ->dependsOn('category')
        ->buildQuery(function ($request, $query, $value, $attribute) {
            $query->where($attribute, $value)
                ->where('status', 'active');
        }),
    ...
];
```


If you want to change the resource format, use the `formatResource` method.
This can be useful if you want to group items.

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    BelongsToDependency::make('Product')
        ->dependsOn('category')
        ->formatResource(function ($resource) {
            return [
                'display' => $resource->name,
                'value' => $resource->id,
                'group' => $resource->parent_category,
            ];
        })
    ...
];
```



## License

The MIT License (MIT). Please see License File for more information.
