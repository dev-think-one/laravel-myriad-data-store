<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyriadOrderPackageType extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.order_package_types');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(MyriadOrderPackage::class, 'order_package_type_id', 'id');
    }
}
