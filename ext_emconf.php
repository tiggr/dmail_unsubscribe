<?php

$EM_CONF['dmail_unsubscribe'] = [
    'title' => 'Direct mail unsubscription',
    'description' => 'Allows a direct mail recipient to unsubscribe himself from receiving direct mail newsletters. The recipient might come from fe_users or tt_address table.',
    'author' => 'Roman Buechler',
    'author_email' => 'rb@synac.com',
    'author_company' => 'Synac Technology, S.L.',
    'category' => 'plugin',
    'version' => '11.5.0',
    'state' => 'beta',
    'constraints' => [
        'depends' => [
            'typo3' => '*',
        ],
    ],
];
