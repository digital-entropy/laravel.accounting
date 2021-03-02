<?php


namespace DigitalEntropy\Accounting\Contracts;


use Illuminate\Database\Eloquent\Relations\HasMany;

interface Account
{
    const TYPE_ASSET = "ASSET";
    const TYPE_LIABILITY = "LIABILITY";
    const TYPE_EQUITY = "EQUITY";
    const TYPE_REVENUE = "REVENUE";
    const TYPE_EXPENSE = "EXPENSE";
    const TYPE_OTHER = "OTHER";

    /**
     * Unique identifier for account.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Account code.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Description of an account.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Is account cash?
     *
     * @return bool
     */
    public function isCash(): bool;

    /**
     * Get account type.
     *
     * @return string
     */
    public function getAccountTypeCode(): string;

    /**
     * Get account type description.
     *
     * @return string
     */
    public function getAccountTypeDescription(): string;

    /**
     * Get debit balance.
     *
     * @return int
     */
    public function getDebit(): int;

    /**
     * Get credit balance.
     *
     * @return int
     */
    public function getCredit(): int;

    /**
     * Define relationship with journal entries.
     *
     * @return HasMany
     */
    public function entries(): HasMany;

    /**
     * Get account group code.
     *
     * @return string
     */
    public function getGroupCode(): string;

}
