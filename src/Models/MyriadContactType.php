<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MyriadDataStore\MyriadDataDownloader;

class MyriadContactType extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'requires_titles' => 'bool',
    ];

    public function getTable()
    {
        return config('myriad-data-store.tables.contact_types');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(MyriadDataDownloader::$contactModel, 'contact_type_id', 'id');
    }
}
