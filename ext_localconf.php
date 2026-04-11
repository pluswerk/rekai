<?php
declare(strict_types=1);

use Pluswerk\Rekai\Controller\QnaController;
use Pluswerk\Rekai\Controller\RecommendationsController;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
    'rekai',
    'Recommendations',
    [RecommendationsController::class => 'show'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'rekai',
    'Qna',
    [QnaController::class => 'show'],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionManagementUtility::addTypoScriptConstants(
    '@import "EXT:rekai/Configuration/TypoScript/constants.typoscript"',
);

ExtensionManagementUtility::addTypoScriptSetup(
    '@import "EXT:rekai/Configuration/TypoScript/setup.typoscript"',
);
