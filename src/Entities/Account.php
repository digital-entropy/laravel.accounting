<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Account
 *
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int id
 */
class Account extends Model implements \DigitalEntropy\Accounting\Contracts\Account
{
    use SoftDeletes;

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
        return $this->attributes['type_code'];
    }

    /**
     * Get account type description.
     *
     * @return string
     */
    function getAccountTypeDescription(): string
    {
        return $this->attributes['type_description'];
    }

    /**
     * Get account group code.
     *
     * @return string
     */
    public function getGroupCode(): string
    {
        return $this->attributes['group_code'];
    }

    /**
     * Get debit balance.
     *
     * @return int
     */
    public function getDebit(): int
    {
        return $this->getAttribute('debit') ?? 0;
    }

    /**
     * Get credit balance.
     *
     * @return int
     */
    public function getCredit(): int
    {
        return $this->getAttribute('credit') ?? 0;
    }

    /**
     * Get balance custom accessor.
     *
     * @return float|int
     */
    public function getBalanceAttribute()
    {
        return abs($this->getDebit() - $this->getCredit());
    }

    /**
     * Get hand_side custom accessor.
     *
     * @return string
     */
    public function getHandSideAttribute()
    {
        return $this->leftHandSide() ? "left" : "right";
    }

    /**
     * Check if account is left hand side
     *
     * @return bool
     */
    public function leftHandSide()
    {
        $types = array_flip(config('accounting.account_types'));
        $currentAccountType = $types[$this->getAccountTypeCode()];

        return array_key_exists($currentAccountType, config('accounting.left'));
    }

    /**
     * Check if account is right hand side
     *
     * @return bool
     */
    public function rightHandSide()
    {
        $types = array_flip(config('accounting.account_types'));
        $currentAccountType = $types[$this->getAccountTypeCode()];

        return array_key_exists($currentAccountType, config('accounting.right'));
    }

    /**
     * Define relationship with journal entries.
     *
     * @return HasMany
     */
    public function entries(): HasMany
    {
        return $this->hasMany(config('accounting.models.entry'));
    }
}
