<?php defined('TYPO3_MODE') or die ('Access denied.');

$TCA['tt_content']['types']['list']['subtypes_excludelist']['dmail_unsubscribe_pi1']='layout,select_key';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:dmail_unsubscribe/locallang_db.xml:tt_content.list_type_pi1',
    'dmail_unsubscribe' . '_pi1',
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dmail_unsubscribe') . 'ext_icon.gif'
),'list_type');
