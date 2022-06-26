<?php

namespace Aqjw\BelongsToDependency;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Closure;

class BelongsToDependency extends BelongsTo
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'belongs-to-dependency';

    public $buildQuery;

    public $formatResource;

    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute, $resource);

        $this->buildQuery($this->defaultFilterableCallback());
    }


    /**
     * Format the given associatable resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return array
     */
    public function formatAssociatableResource(NovaRequest $request, $resource)
    {
        if(! $this->formatResource) {
            return parent::formatAssociatableResource($request, $resource);
        }

        return call_user_func($this->formatResource, $resource);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        if (array_key_exists($this->attribute, $resource->getRelations()) && $resource->relationLoaded($this->attribute)) {
            $value = $resource->{$this->attribute};
        } else {
            $value = $resource->{$this->attribute}()->withoutGlobalScopes()->setEagerLoads([])->first();
        }

        if ($value) {
            $this->belongsToId = $value->getKey();

            $this->value = $this->formatDisplayValue($value);
        }
    }

    /**
     * Set build query callback
     * 
     * @param closure $buildQuery
     * @return $this
     */
    public function buildQuery($buildQuery)
    {
        $this->buildQuery = $buildQuery;

        return $this;
    }

    /**
     * Set format resource callback
     * 
     * @param closure $formatResource
     * @return $this
     */
    public function formatResource(Closure $formatResource)
    {
        $this->formatResource = $formatResource;

        return $this;
    }

    /**
     * Build an associatable query for the field.
     * Here is where we add the depends on value and filter results
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  bool  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAssociatableQuery(NovaRequest $request, $withTrashed = false)
    {
        $query = parent::buildAssociatableQuery($request, $withTrashed);

        if($request->has('dependsOnValue')) {
            call_user_func(
                $this->buildQuery,
                $request, $query->toBase(), $request->dependsOnValue, $this->meta['dependsOnKey']
            );
        }

        return $query;
    }

    /**
     * Set the depends on field and depends on key
     *
     * @param  string $dependsOnField
     * @param  string $tableKey
     * @return $this
     */
    public function dependsOn($dependsOnField, $tableKey = null)
    {
        return $this->withMeta([
            'dependsOn' => $dependsOnField,
            'dependsOnKey' => $tableKey ?: strtolower($dependsOnField).'_id',
        ]);
    }
}
