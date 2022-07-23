<?php
namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MyriadDataStore\MyriadDataDownloader;

/**
 * @property \JsonFieldCast\Json\SimpleJsonField $details
 */
class MyriadOrder extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'details'    => \JsonFieldCast\Casts\SimpleJsonField::class,
        'order_date' => 'date',
    ];

    public function getTable()
    {
        return config('myriad-data-store.tables.orders');
    }

    public function getNameAttribute(): string
    {
        return $this->order_date?->format('jS F Y') ?? '-';
    }

    public function packages(): HasMany
    {
        return $this->hasMany(MyriadOrderPackage::class, 'order_id', 'id');
    }

    public function despatchContact(): BelongsTo
    {
        return $this->belongsTo(MyriadDataDownloader::$contactModel, 'despatch_contact_id', 'id');
    }

    public function invoiceContact(): BelongsTo
    {
        return $this->belongsTo(MyriadDataDownloader::$contactModel, 'invoice_contact_id', 'id');
    }

    public function agentContact(): BelongsTo
    {
        return $this->belongsTo(MyriadDataDownloader::$contactModel, 'agent_contact_id', 'id');
    }
}
