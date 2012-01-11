<?php

/**
 * 
 * @version 0.1.20120109
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.uimodel
 */
class BoxFactory {

    protected $_cachePath;
    protected $_cacheExpire;
    protected $_cacheCategory;
    protected $_cacheKey;
    protected $_boxType;
    protected $_html;

    function __construct($type) {
        $this->_boxType = $type;
        $this->_html = "";
    }

    public function GetType() {
        return $this->_boxType;
    }

    public function CacheBind() {
        //to be over ridden
        $this->_cachePath = "";
        $this->_cacheCategory = "";
        $this->_cacheKey = "";
        $this->_cacheExpire = -1;
    }

    public function AddBox(BoxModel $box) {
        if ($box->GetType() === $this->_boxType) {
            if (empty($this->_html))
                $this->_html = $box->GetHTML();
            else
                $this->_html.=$box->GetHTML();
        }
    }

    public function DataBind() {
        //to be over ridden
    }

    public function GetHTML() {

        $this->CacheBind();
        if (!empty($this->_cacheCategory)) {

            $cr = new PHPCacheReader($this->_cachePath, $this->_cacheCategory);

            $this->_html = $cr->GetValue($this->_cacheKey);
        }

        if (empty($this->_html)) {
            $this->DataBind();

            if (!empty($this->_cacheCategory)) {

                $ce = new PHPCacheEditor($this->_cachePath, $this->_cacheCategory);
                $ce->SetValue($this->_cacheKey, $this->_html, $this->_cacheExpire);
                $ce->Save();
            }
        }
        return $this->_html;
    }

}

?>
