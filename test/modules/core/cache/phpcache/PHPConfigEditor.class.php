<?php
//TODO a simplified version, without version, expiry time features
class PHPConfigEditor {

    protected $category;

    function __construct($categoryname) {
        GLOBAL $_cachedData;
        $this->category = $categoryname;
        $cachefile = "cache/{$categoryname}.php";
        if (is_file($cachefile))
            require($cachefile);
        if (!isset($_cachedData[$categoryname]))
            $_cachedData[$categoryname] = array();
    }

    public function SetValue($key, $value) {
        GLOBAL $_cachedData;
        //TODO validate key, e.g. no quots
        //for efficiency, skip the step since key is an internal input
        $_cachedData[$this->category][$key] = $value;
    }

    public function Save() {

        GLOBAL $_cachedData;

        $fp = @fopen("cache/{$this->category}.php", "w");
        if (!$fp)
            return FALSE;

        fwrite($fp, "<?php\n");
        foreach ($_cachedData[$this->category] as $key => $value) {
            fwrite($fp, "\$_cachedData[\"{$this->category}\"][\"{$key}\"]=");
            if (is_string($value))
                fwrite($fp, toScriptString($value) . ";\n");
            else if (is_bool($value))
                fwrite($fp, ($value ? "TRUE" : "FALSE") . ";\n");
            else
                fwrite($fp, $value . ";\n");
        }
        fwrite($fp, "?>");

        fclose($fp);

        return TRUE;
    }

}
