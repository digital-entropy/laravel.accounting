<?php


namespace DigitalEntropy\Accounting\Entities;


use DigitalEntropy\Accounting\AccountingManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int id
 */
class Account extends Model
{

    const TYPE_ASSET = "ASSET";
    const TYPE_LIABILITY = "LIABILITY";
    const TYPE_EQUITY = "EQUITY";
    const TYPE_REVENUE = "REVENUE";
    const TYPE_EXPENSE = "EXPENSE";
    const TYPE_OTHER = "OTHER";

    const TYPE_CASH = "CASH";
    const TYPE_NON_CASH = "NON_CASH";

    protected $fillable = [
        'code',
        'group_code',
        'description',
        'type_code',
        'type_description',
        'is_cash'
    ];

    protected $appends = [
        'combined_code'
    ];

    public function getCombinedCodeAttribute(AccountingManager $accounting)
    {
        return implode('', [
            $this->attributes['type_code'],
            $accounting->getTypeSeparator(),
            $this->attributes['code'],
            $accounting->getGroupSeparator(),
            $this->attributes['group_code']
        ]);
    }

}