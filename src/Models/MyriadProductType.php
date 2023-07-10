<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyriadProductType extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.product_types');
    }

    public function titles(): HasMany
    {
        return $this->hasMany(MyriadTitle::class, 'product_type_id', 'id');
    }
}
