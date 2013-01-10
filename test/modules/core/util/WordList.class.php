<?php
LoadIBC1Class('ItemList', 'util');
/**
 * 
 * @version 0.7.20120310
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.common
 */
class WordList extends ItemList {

    function __construct($w = "") {
        if ($w != "")
            $this->SetWords($w);
    }

    public function SetWords($w) {
        $this->Clear();
        $this->AddWords($w);
    }

    public function AddWords($w) {

        $w = str_replace("%", " ", $w);

        $t = array(chr(10),
            chr(13),
            " ",
            ",",
            ".",
            "?",
            "!",
            ":",
            ";",
            "<",
            ">",
            "[",
            "]",
            "{",
            "}",
            "'",
            "\"",
            "|",
            "　",
            "，",
            "。",
            "？",
            "！",
            "：",
            "；",
            "《",
            "》",
            "〉",
            "〈",
            "‘",
            "’",
            "“",
            "”"
        );

        foreach ($t as $item) {
            $w = str_replace($item, "%", $w);
        }
        $wa = explode("%", $w);
        foreach ($wa as $item) {
            if ($item != "")
                $this->AddItem($item);
        }
    }

    public function GetWords() {
        $w = "";
        while ($item = $this->GetEach()) {
            $w.=$item . " ";
        }
        return substr($w, 0, -1);
    }

    public function HasWord($w, $casesensitive = FALSE) {
        $this->MoveFirst();
        if ($casesensitive) {
            while ($item = $this->GetEach()) {
                if ($w == $item)
                    return TRUE;
            }
        }
        else {
            while ($item = $this->GetEach()) {
                if (strtolower($w) == strtolower($item))
                    return TRUE;
            }
        }
        return FALSE;
    }

}
