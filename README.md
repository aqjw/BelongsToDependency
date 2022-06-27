# BelongsTo field with dependency support for Nova

This package is an extension of Laravel Nova's existing BelongsTo field and Vue components.

## Requirements

- PHP 8.0.2+
- Nova 4+


## Table of Contents

 - [Installation](#installation)
 - [Usage](#usage)
    - [Methods](#methods)
        - [dependsOn](#dependson)
        - [searchable](#searchable)
        - [buildQuery](#buildquery)
        - [formatResource](#formatresource)
 - [License](#license)

### Installation

This package can be installed via command:

```bash
composer require aqjw/belongs-to-dependency
```

### Usage

The following will list categories with `type_id` equal to the value set in the first BelongsTo field.

```php
use Aqjw\BelongsToDependency\BelongsToDependency;
...
return [
    ...
    BelongsTo::make('Type'),
    
    BelongsToDependency::make('User')
        ->dependsOn('type', 'type_id')
    ...
];
```

### Methods

#### dependsOn

The method can take one or two parameters.
If you don't pass the second parameter, it will be generated from first one with the `_id` suffix.

This may depend on `BelognsTo`, `Text`, `Enum`, and others 🤷

```php
BelongsToDependency::make('User')
    ->dependsOn('type', 'type_id'),
```

##### Multiple dependency

You can also pass an array as the first parameter. To make a dependency on two or more fields.

```php
BelongsToDependency::make('User')
    ->dependsOn(['type', 'role']),
```

Or

```php
BelongsToDependency::make('User')
    ->dependsOn([
        'type' => 'type_id',
        'role' => 'role_id',
    ]),
```


#### searchable

Use this method if you have many entries.

```php
BelongsTo::make('Country')
    ->searchable(),

BelongsToDependency::make('State')
    ->searchable()
    ->dependsOn('country'),

BelongsToDependency::make('City')
    ->searchable()
    ->dependsOn('state'),
```


#### buildQuery

If you want to add your own conditions to the query then greet the `buildQuery` method.

```php
BelongsToDependency::make('Product')
    ->dependsOn('category')
    ->buildQuery(function ($query, $values) {
        $query->where($values)
            ->where('status', 'active');
    }),
```


#### formatResource

Use this method to change the resource format.
This can be useful if you want to group items.

```php
BelongsToDependency::make('Product')
    ->dependsOn('category')
    ->formatResource(function ($resource) {
        return [
            'display' => $resource->name,
            'value' => $resource->id,
            'group' => $resource->parent_category,
        ];
    })
```



### License

The MIT License (MIT). Please see License File for more information.
