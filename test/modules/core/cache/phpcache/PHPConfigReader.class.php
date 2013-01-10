<?php

//TODO a simplified version, without version, expiry time features

class PHPConfigReader {

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

    public function GetValue($key) {
        GLOBAL $_cachedData;
        //TODO validate key, e.g. no quots
        //for efficiency, skip the step since key is an internal input
        return $_cachedData[$this->category][$key];
    }

}
