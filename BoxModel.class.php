<?php

/**
 * a box container model
 * @version 0.5.20120112
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.uimodel
 */
class BoxModel extends UIModel {

    private $_type;
    private $_width;
    private $_height;
    private $_title;
    private $_content;
    private $_padding;
    private $_align;
    private $_valign;
    protected $_tplName;
    protected $_cachePath;
    protected $_cacheExpire;
    protected $_cacheCategory;
    protected $_cacheKey;
    protected $_cacheVersion;
    protected $_classname;

    function __construct($t) {
        $this->_height = "";
        $this->_title = "";
        $this->_content = "";
        $this->_padding = 0;
        $this->_align = "left";
        $this->_valign = "top";
        $this->_classname = __CLASS__;
        $this->SetType($t);
    }

    public function SetType($t) {
        $this->_type = $t;
    }

    public function GetType() {
        return $this->_type;
    }

    public function SetWidth($w) {
        $this->_width = $w;
    }

    public function SetHeight($h) {
        $this->_height = $h;
    }

    public function SetTitle($text) {
        $this->_title = $text;
    }

    public function SetAlign($align, $valign) {

        $this->_align = $align;
        $this->_valign = $valign;
    }

    public function SetPadding($padding) {
        $this->_padding = $padding;
    }

    public function SetContent($html, $align=NULL, $valign=NULL, $padding=-1) {
        $this->_content = $html;
        if ($align != NULL)
            $this->_align = $align;
        if ($valign != NULL)
            $this->_valign = $valign;
        if ($padding > -1)
            $this->_padding = $padding;
    }

    public function AppendContent($html) {
        $this->_content .= $html;
    }

    public function CacheBind() {
        //to be over ridden
        $this->_cachePath = "";
        $this->_cacheCategory = "";
        $this->_cacheKey = "";
        $this->_cacheExpire = 0;
        $this->_cacheVersion = 0;
    }

    public function DataBind() {
        //to be over ridden
    }

    private function GetRefreshedHTML() {

        $this->DataBind();

        $html = "";
        if ($this->_tplName != "") {
            $html = $this->TransformTpl($this->_tplName, array(
                "Width" => $this->_width,
                "Height" => $this->_height,
                "Title" => $this->_title,
                "Content" => $this->_content,
                "Padding" => $this->_padding,
                "Align" => $this->_align,
                "VAlign" => $this->_valign
                    ), $this->_classname);
        } else {
            $html = $this->_content;
        }

        if (!empty($this->_cacheCategory)) {

            $ce = new PHPCacheEditor(GetCachePath(TRUE), $this->_cacheCategory);
            $ce->SetValue($this->_cacheKey, $html, $this->_cacheExpire, $this->_cacheVersion > 0);
            $ce->Save();
        }

        return $html;
    }

    public function GetHTML() {

        $this->CacheBind();
        if (!empty($this->_cacheCategory)) {

            $cr = new PHPCacheReader($this->_cachePath, $this->_cacheCategory);
            $cr->SetRefreshFunction(array($this, "GetRefreshedHTML"));
            return $cr->GetValue($this->_cacheKey, $this->_cacheVersion);
        } else {
            $this->GetRefreshedHTML();
        }
    }

}

?>
