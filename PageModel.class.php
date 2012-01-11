<?php

/**
 * a generic page model, based on a simple php string template
 * @version 0.4.20120109
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.uimodel
 */
abstract class PageModel extends UIModel {

    /**
     * name of page template
     * @var string
     */
    protected $_pagetpl = "";
    protected $_title = "";
    protected $_keywords = "";
    protected $_description = "";
    protected $_meta = "";
    protected $_css = "";
    protected $_cssfile = array();
    protected $_js = "";
    protected $_jsfile = array();
    protected $_icon = NULL;
    protected $_regions = array();
    protected $_classname;

    /**
     * constructor
     * @param string $pagetpl   name of page template
     * @see $_pagetpl
     */
    function __construct($pagetpl) {
        $this->_pagetpl = $pagetpl;
        $this->_classname = __CLASS__;
        $this->Initialize();
    }

    /**
     * automatically called during constructing the object
     *  e.g. start timer, prepare page header
     */
    protected function Initialize() {
        //do nothing, but overrideable
    }

    /**
     * automatically involked when outputing html
     * e.g. set page footer, stop timer
     */
    protected function Finalize() {
        //do nothing, but overrideable
    }

    /**
     * set title for the page
     * @param string $title
     */
    public function SetTitle($title) {
        $this->_title = htmlspecialchars($title);
    }

    /**
     * add keywords in &lt;meta&gt;
     * @param string $keywords 
     */
    public function AddKeywords($keywords) {
        $wl = new WordList($keywords);
        if ($this->_keywords != "")
            $this->_keywords.=" ";
        $this->_keywords .= $wl->GetWords();
    }

    /**
     * set description in &lt;meta&gt;
     * @param string $desc 
     */
    public function SetDescription($desc) {
        $wl = new WordList($desc);
        if ($this->_description != "")
            $this->_description.=" ";
        $this->_description .= $wl->GetWords();
    }

    /**
     * add extra &lt;meta&gt; information
     * @param string $name
     * @param string $content 
     */
    public function AddMeta($name, $content) {
        $name = htmlspecialchars($name);
        $content = htmlspecialchars($content);
        $this->_meta.="<meta content=\"$content\" name=\"$name\" />\n";
    }

    /**
     * append CSS code fragment
     * @param string $css 
     */
    public function AppendCSS($css) {
        $this->_css.=$css;
    }

    /**
     * add a CSS file
     * @param string $cssfile 
     */
    public function AddCSSFile($cssfile) {
        if ($cssfile != "")
            $this->_cssfile[] = $cssfile;
    }

    /**
     * append JavaScript code fragment to &lt;head&gt;
     * @param string $js 
     */
    public function AppendJS($js) {
        $this->_js.=$js;
    }

    /**
     * add JavaScript file to &lt;head&gt; from /scripts directory
     * @param string $jsfile 
     */
    public function AddJSFile($jsfile) {
        if ($jsfile != "")
            $this->_jsfile[] = $jsfile;
    }

    /**
     * set an image as an icon
     * @param stirng $iconfile 
     */
    public function SetIcon($iconfile, $type="") {
        if ($type == "")
            $type = GetFileExt($iconfile);
        switch (strtolower($type)) {
            case "ico":
                $mime = "image/x-icon";
                break;
            case "jpeg":
            case "jpg":
                $mime = "image/jpeg";
                break;
            case "gif":
                $mime = "image/gif";
                break;
            case "png":
                $mime = "image/png";
                break;
            case "bmp":
                $mime = "image/bmp";
                break;
            default:
                $mime = $type;
        }
        $this->_icon = array($iconfile, $mime);
    }

    /**
     *
     * @param string $region
     * @param BoxFactory $factory
     */
    public function AddBoxFactory($region, BoxFactory $factory) {
        if (isset($this->_regions[$region]))
            $this->_regions[$region].=$factory->GetHTML();
        else
            $this->_regions[$region] = $factory->GetHTML();
    }

    /**
     *
     * @param string $region
     * @param BoxModel $box 
     */
    public function AddBox($region, BoxModel $box) {
        if (isset($this->_regions[$region]))
            $this->_regions[$region].=$box->GetHTML();
        else
            $this->_regions[$region] = $box->GetHTML();
    }

    public function GetHTML() {
        //head BEGIN
        $head = "";
        //icon
        if ($this->_icon != NULL) {
            $head.="<link rel=\"shortcut icon\" type=\"{$this->_icon[1]}\" href=\"{$this->_icon[0]}\" />\n";
        }
        //css
        foreach ($this->_cssfile as $cssfile) {
            if ($cssfile != "")
                $head.="<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cssfile}\" />\n";
        }
        if ($this->_css != "")
            $head.="<style>\n{$this->_css}\n</style>\n";
        //js
        foreach ($this->_jsfile as $jsfile) {
            if ($jsfile != "")
                $head.="<script language=\"javascript\" type=\"text/javascript\" src=\"{$jsfile}\"></script>\n";
        }
        if ($this->_js != "")
            $head.="<script language=\"javascript\" type=\"text/javascript\">\n{$this->_js}\n</script>\n";
        //meta
        $head.=$this->_meta;
        //head END
        //output
        $this->_regions["Keywords"] = $this->_keywords;
        $this->_regions["Description"] = $this->_description;
        $this->_regions["Title"] = $this->_title;
        $this->_regions["Head"] = $head;
        $this->Finalize();
        return $this->TransformTpl($this->_pagetpl, $this->_regions, $this->_classname);
    }

    public function Show() {
        echo $this->GetHTML();
    }

}

?>
