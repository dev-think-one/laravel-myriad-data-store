<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyriadTitle extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'active' => 'bool',
    ];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.titles');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(MyriadOrderPackage::class, 'title_id', 'id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(MyriadIssue::class, 'title_id', 'id');
    }

    public function currentIssue(): BelongsTo
    {
        return $this->belongsTo(MyriadIssue::class, 'current_issue_id', 'id');
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(MyriadProductType::class, 'product_type_id', 'id');
    }
}
