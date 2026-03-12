<?php

return [
    'notify_email' => env('REQUESTS_NOTIFY_EMAIL', 'josinelde.coelho@egp.ce.gov.br'),
    'training_notify_emails' => [
        'state' => [
            env('REQUESTS_TRAINING_NOTIFY_EMAIL_STATE_1', 'josinelde.coelho@egp.ce.gov.br'),
            env('REQUESTS_TRAINING_NOTIFY_EMAIL_STATE_2', 'rodrigo.lopes@egp.ce.gov.br'),
            env('REQUESTS_TRAINING_NOTIFY_EMAIL_STATE_3', 'joao.bastos@egp.ce.gov.br'),
        ],
        'municipality' => [
            env('REQUESTS_TRAINING_NOTIFY_EMAIL_MUNICIPALITY_1', 'rodrigo.lopes@egp.ce.gov.br'),
            env('REQUESTS_TRAINING_NOTIFY_EMAIL_MUNICIPALITY_2', 'joao.bastos@egp.ce.gov.br'),
        ],
    ],
    'space_notify_emails' => [
        env('REQUESTS_SPACE_NOTIFY_EMAIL_1', 'josinelde.coelho@egp.ce.gov.br'),
        env('REQUESTS_SPACE_NOTIFY_EMAIL_2', 'rodrigo.lopes@egp.ce.gov.br'),
        env('REQUESTS_SPACE_NOTIFY_EMAIL_3', 'joao.bastos@egp.ce.gov.br'),
    ],
];
