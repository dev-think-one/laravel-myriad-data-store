<?php

namespace MyriadDataStore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyriadIssue extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'publication_date' => 'date',
    ];

    public function getTable(): string
    {
        return config('myriad-data-store.tables.issues');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(MyriadTitle::class, 'title_id', 'id');
    }
}
