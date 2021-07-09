<?php
declare(strict_types=1);

return [
    'instagram:importfeed' => [
        'class' => \In2code\Instagram\Command\ImportFeedCommand::class,
        'schedulable' => true
    ],
    'instagram:refreshtoken' => [
        'class' => \In2code\Instagram\Command\RefreshTokenCommand::class,
        'schedulable' => true
    ],
    'instagram:removetokens' => [
        'class' => \In2code\Instagram\Command\RemoveTokensCommand::class,
        'schedulable' => true
    ],
];
