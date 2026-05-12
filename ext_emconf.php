<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Rek.ai',
    'description' => 'Integrates rek.ai recommendations, Q&A and search autocomplete into TYPO3',
    'category' => 'fe',
    'author' => '+Pluswerk AG',
    'author_email' => 'mike.streibl@pluswerk.digital',
    'state' => 'stable',
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.99.99',
            'extbase' => '12.4.0-14.99.99',
            'fluid' => '12.4.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
