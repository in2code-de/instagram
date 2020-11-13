<?php
declare(strict_types=1);

return [
    'instagram:importfeed' => [
        'class' => \In2code\Instagram\Command\ImportFeedCommand::class,
        'schedulable' => true
    ],
];
