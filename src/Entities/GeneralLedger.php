<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(config('accounting.models.account'));
    }

}
