<?php

/**
 * 
 * @version 0.2.20111204
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.cache
 */
class CacheProvider {

    private $cachepath;
    private $module = IBC1_DEFAULT_CACHE;

    function __construct($cachepath, $module = IBC1_DEFAULT_CACHE) {
        if (!is_dir($cachepath))
            throw new Exception("cannot find the path");
        $this->cachepath = $cachepath;
        $this->module = $module;
    }

    private function GetClass($component, $category) {
        LoadIBC1Class("PHPCacheReader", "cache.phpcache");
        $r = new PHPCacheReader(dirname(__FILE__), "plugin_cache");
        $c = $r->GetValue($this->module);
        $ce = $c[$component];
        if (is_string($ce)) {
            call_user_func("LoadIBC1Class", $ce, "cache.{$this->module}");
            return new $ce($this->cachepath . '/' . $category . '.php', $category);
        } else {
            throw new Exception("cannot find the cache module: {$this->module}");
        }
    }

    public function GetEditor($category) {
        LoadIBC1Class("ICacheEditor", "cache");
        switch ($this->module) {
            case "phpcache":
                LoadIBC1Class("PHPCacheEditor", "cache.phpcache");
                return new PHPCacheEditor($this->cachepath . '/' . $category . '.php', $category);
            default:
                return $this->GetClass("editor", $category);
        }
    }

    public function GetReader($category) {
        LoadIBC1Class("ICacheReader", "cache");
        switch ($this->module) {
            case "phpcache":
                LoadIBC1Class("PHPCacheReader", "cache.phpcache");
                return new PHPCacheReader($this->cachepath . '/' . $category . '.php', $category);
            default:
                return $this->GetClass("reader", $category);
        }
    }

}
