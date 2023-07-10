<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \JsonFieldCast\Json\SimpleJsonField $details
 */
class MyriadOrderPackage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'details' => \JsonFieldCast\Casts\SimpleJsonField::class,
    ];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.order_packages');
    }

    public function getNameAttribute(): string
    {
        return "Package for order {$this->order_id}";
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(MyriadOrder::class, 'order_id', 'id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(MyriadTitle::class, 'title_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(MyriadOrderPackageType::class, 'order_package_type_id', 'id');
    }
}
