<?php

namespace Dentro\Accounting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralLedger extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'general_ledgers';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'credit', 'debit', 'period', 'account_id'
    ];

    /**
     * Define relationship with account Model.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(config('accounting.models.account'));
    }

}
