<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Rek.ai',
    'description' => 'Integrates rek.ai recommendations, Q&A and search autocomplete into TYPO3',
    'category' => 'fe',
    'author' => 'Pluswerk AG',
    'author_email' => 'hello@pluswerk.ag',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-14.99.99',
            'extbase' => '12.0.0-14.99.99',
            'fluid' => '12.0.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
