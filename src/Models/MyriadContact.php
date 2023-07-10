<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *  @property \JsonFieldCast\Json\SimpleJsonField $details
 *  @property \JsonFieldCast\Json\SimpleJsonField $communications
 */
class MyriadContact extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'details'        => \JsonFieldCast\Casts\SimpleJsonField::class,
        'communications' => \JsonFieldCast\Casts\SimpleJsonField::class,
    ];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.contacts');
    }

    public function getNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->details->getAttribute('Title'),
            $this->details->getAttribute('Forename'),
            $this->details->getAttribute('Surname'),
        ]));
    }

    public function contactType(): BelongsTo
    {
        return $this->belongsTo(MyriadContactType::class, 'contact_type_id', 'id');
    }

    public function ordersAsDespatch(): HasMany
    {
        return $this->hasMany(MyriadOrder::class, 'despatch_contact_id', 'id');
    }

    public function ordersAsInvoice(): HasMany
    {
        return $this->hasMany(MyriadOrder::class, 'invoice_contact_id', 'id');
    }

    public function ordersAsAgent(): HasMany
    {
        return $this->hasMany(MyriadOrder::class, 'agent_contact_id', 'id');
    }
}
