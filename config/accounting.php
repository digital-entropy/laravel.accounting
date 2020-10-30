<?php

use DigitalEntropy\Accounting\Contracts\Account;

return [
    // separators x-xxx.xx
    'separators' => [
        'type' => '-',
        'group' => '.'
    ],
    'account_types' => [
        '1' => Account::TYPE_ASSET,
        '2' => Account::TYPE_LIABILITY,
        '3' => Account::TYPE_EQUITY,
        '4' => Account::TYPE_REVENUE,
        '5' => Account::TYPE_EXPENSE,
        '6' => Account::TYPE_OTHER
    ],
    'statements' => [
        'balance_sheets' => [
            'cash_only' => false,
            'left' => ['1'],
            'right' => ['2', '3']
        ],
        'profit_loss' => [
            'cash_only' => false,
            'left' => ['4'],
            'right' => ['5', '6']
        ],
        'cash_flow' => [
            'cash_only' => true,
            'left' => ['1'],
            'right' => ['2', '3']
        ]
    ]
];