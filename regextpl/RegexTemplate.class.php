<?php

/**
 *
 * @version 0.3
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.template.regextpl
 */
class RegexTemplate extends Template {

    protected function Tpl2HTML($tpl, $vars, $tplclass="") {
        foreach ($vars as $key => $value) {
            $pattern = "\{ *RegexTpl";
            if ($tplclass != "") {
                $pattern.="_" . $tplclass;
            }
            $pattern.="_Field *= *" . $key . " *\}";
            $tpl = preg_replace($pattern, $value, $tpl);
        }
    }

}

?>
