<?php
declare(strict_types=1);

return [
    'frontend' => [
        'pluswerk/rekai/script-injection' => [
            'target' => \Pluswerk\Rekai\Middleware\RekaiScriptMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/output-compression',
            ],
        ],
    ],
];
