<?php


namespace DigitalEntropy\Accounting\Contracts;


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
    function getIdentifier(): string;

    /**
     * Account code.
     *
     * @return string
     */
    function getCode(): string;

    /**
     * Description of an account.
     *
     * @return string
     */
    function getDescription(): string;

    /**
     * Is account cash?
     *
     * @return bool
     */
    function isCash(): bool;

    /**
     * Get account type.
     *
     * @return string
     */
    function getAccountTypeCode(): string;

    /**
     * Get account type description.
     *
     * @return string
     */
    function getAccountTypeDescription(): string;

}
