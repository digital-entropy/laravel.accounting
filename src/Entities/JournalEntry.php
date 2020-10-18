<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $table = 'journals_entries';

    protected $fillable = [
        'type',
        'memo',
        'ref'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function journal() {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }

}