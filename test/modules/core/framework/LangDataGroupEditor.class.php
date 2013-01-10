<?php

LoadIBC1Class('AbstractDataGroupEditor', 'framework');

/**
 * A tool for programatically editing locale data.
 * 
 * @version 0.1.20130110
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
class LangDataGroupEditor extends AbstractDataGroupEditor {

    function __construct($group = NULL) {

        // read language code
        $lang = &$GLOBALS[IBC1_PREFIX . '_Language'];
        if (!isset($lang))
            $lang = GetLang();

        // data group
        if (empty($group))
            $group = $lang;
        else
            $group = $lang . '_' . $group;

        // load a writer
        LoadIBC1Class('ICacheEditor', 'cache');
        LoadIBC1Class('PHPCacheEditor', 'cache.phpcache');
        $this->editor = new PHPCacheEditor("lang/$lang/$group.lang.php", $group);
    }

}
