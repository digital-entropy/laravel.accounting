<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 *
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int id
 */
class Account extends Model implements \DigitalEntropy\Accounting\Contracts\Account
{
    protected $fillable = [
        'code',
        'description',
        'group_code',
        'group_description',
        'type_code',
        'type_description',
        'is_cash'
    ];

    function getIdentifier(): string
    {
        return $this->getKeyName();
    }

    function getCode(): string
    {
        return $this->attributes['code'];
    }

    function getDescription(): string
    {
        return $this->attributes['description'];
    }

    function isCash(): bool
    {
        return $this->attributes['is_cash'];
    }

    function getAccountTypeCode(): string
    {
        return $this->attributes['account_type_code'];
    }

    function getAccountTypeDescription(): string
    {
        return $this->attributes['account_type_description'];
    }
}