<?php

require 'modules/core/core1.lib.php';
LoadIBC1Lib('common', 'framework');
require GetSysResPath('TestPage.class.php', 'modules/pages');

$p = new TestPage();
$p->Prepare(array(
    'box' => array('Welcome', NULL),
    'modules' => array(
        'theme' => array(
            'box' => array('ThemeList', NULL),
            'functions' => array(
                'select' => array('SelectTheme', NULL)
            )
        ),
        'language' => array(
            'box' => array('LangList', NULL),
            'functions' => array(
                'select' => array('SelectLang', NULL)
            )
        )
    )
));
$p->Show();