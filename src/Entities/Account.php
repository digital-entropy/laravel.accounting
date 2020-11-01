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
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'code',
        'description',
        'group_code',
        'group_description',
        'type_code',
        'type_description',
        'is_cash'
    ];

    /**
     * Unique identifier for account.
     *
     * @return string
     */
    function getIdentifier(): string
    {
        return $this->getKeyName();
    }

    /**
     * Account code.
     *
     * @return string
     */
    function getCode(): string
    {
        return $this->attributes['code'];
    }

    /**
     * Description of an account.
     *
     * @return string
     */
    function getDescription(): string
    {
        return $this->attributes['description'];
    }

    /**
     * Is account cash?
     *
     * @return bool
     */
    function isCash(): bool
    {
        return $this->attributes['is_cash'];
    }

    /**
     * Get account type.
     *
     * @return string
     */
    function getAccountTypeCode(): string
    {
        return $this->attributes['account_type_code'];
    }

    /**
     * Get account type description.
     *
     * @return string
     */
    function getAccountTypeDescription(): string
    {
        return $this->attributes['account_type_description'];
    }
}
