<?php

$EM_CONF['dmail_unsubscribe'] = [
    'title'              => 'Direct mail unsubscription',
    'description'        => 'Allows a direct mail recipient to unsubscribe himself from receiving direct mail newsletters. The recipient might come from fe_users or tt_address table.',
    'category'           => 'plugin',
    'version'            => '9.5.0',
    'state'              => 'beta',
    'uploadfolder'       => 0,
    'createDirs'         => '',
    'modify_tables'      => '',
    'clearcacheonload'   => 0,
    'author'             => 'Roman Buechler',
    'author_email'       => 'rb@synac.com',
    'author_company'     => 'Synac Technology, S.L.',
    'constraints'        =>
        [
            'depends'   =>
                [
                ],
            'conflicts' => '',
            'suggests'  =>
                [
                ],
        ],
];
