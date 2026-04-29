<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'label' => 'rek.ai',
        'value' => '--div--',
    ],
);

ExtensionUtility::registerPlugin(
    'rekai',
    'Recommendations',
    'Rek.ai Recommendations',
    'content-widget-text',
    'rekai',
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:rekai/Configuration/FlexForms/Recommendations.xml',
    'rekai_recommendations',
);

$GLOBALS['TCA']['tt_content']['types']['rekai_recommendations']['showitem'] =
    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,'
    . '--palette--;;general,'
    . '--palette--;;headers,'
    . 'pi_flexform,'
    . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,'
    . '--palette--;;hidden,'
    . '--palette--;;access';

ExtensionUtility::registerPlugin(
    'rekai',
    'Qna',
    'Rek.ai Q&A',
    'content-widget-text',
    'rekai',
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:rekai/Configuration/FlexForms/Qna.xml',
    'rekai_qna',
);

$GLOBALS['TCA']['tt_content']['types']['rekai_qna']['showitem'] =
    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,'
    . '--palette--;;general,'
    . '--palette--;;headers,'
    . 'pi_flexform,'
    . '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,'
    . '--palette--;;hidden,'
    . '--palette--;;access';
