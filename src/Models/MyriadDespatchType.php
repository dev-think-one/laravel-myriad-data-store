<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;

class MyriadDespatchType extends Model
{
    const CATEGORY_TELEPHONE              = 1;
    const CATEGORY_FAX                    = 2;
    const CATEGORY_EMAIL                  = 3;
    const CATEGORY_POST                   = 4;
    const CATEGORY_PRESSTREAM_OR_MAILSORT = 5;
    const CATEGORY_WEBSITE                = 6;
    const CATEGORY_SALES_LEAD_EMAIL       = 7;

    public $incrementing = false;

    protected $guarded = [];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.despatch_types');
    }
}
