<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 *
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
        'description',
        'group_code',
        'group_description',
        'type_code',
        'type_description',
        'is_cash'
    ];

}