<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;

class MyriadOrderStatusType extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.order_status_types');
    }
}
