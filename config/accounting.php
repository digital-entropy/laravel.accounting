<?php

use DigitalEntropy\Accounting\Entities\Account;

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
            'cash_condition' => [
                Account::TYPE_CASH,
                Account::TYPE_NON_CASH
            ],
            'left' => [
                Account::TYPE_ASSET
            ],
            'right' => [
                Account::TYPE_LIABILITY,
                Account::TYPE_EQUITY
            ]
        ],
        'profit_loss' => [
            'cash_condition' => [
                Account::TYPE_CASH,
                Account::TYPE_NON_CASH
            ],
            'left' => [
                Account::TYPE_REVENUE
            ],
            'right' => [
                Account::TYPE_EXPENSE,
                Account::TYPE_OTHER
            ]
        ],
        'cash_flow' => [
            'cash_condition' => [
                Account::TYPE_CASH
            ],
            'left' => [
                Account::TYPE_ASSET
            ],
            'right' => [
                Account::TYPE_LIABILITY,
                Account::TYPE_EQUITY
            ]
        ]
    ]
];