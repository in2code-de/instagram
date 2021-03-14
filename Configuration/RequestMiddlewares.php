<?php
return [
    'frontend' => [
        'instagram-getauthenticationcode' => [
            'target' => \In2code\Instagram\Middleware\GetAuthenticationCode::class,
            'before' => [
                'typo3/cms-frontend/timetracker'
            ]
        ]
    ]
];
