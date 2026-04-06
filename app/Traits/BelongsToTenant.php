<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Automatically scopes every query to the current tenant and sets the
// tenant_id column when a new model is created. Apply this trait to any
// Eloquent model that stores tenant-specific data.
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // Automatically assign the current tenant when creating a record
        // without an explicit tenant_id value.
        static::creating(function ($model) {
            if (! $model->tenant_id && $tenant = Tenant::current()) {
                $model->tenant_id = $tenant->id;
            }
        });

        // Global scope that filters all queries for this model so only rows
        // belonging to the active tenant are returned. Uses the full table
        // name to avoid ambiguity on joined queries.
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenant->id);
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
