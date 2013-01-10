<?php

/**
 * The main library for InterBox Framework.
 *
 * It provides a set of functions for resource management that enables multiple
 * themes and languages, and a Page-Process-Box model that separates logics from
 * views, with the cache module in the Core to help increase performance.
 * Note that the library is dependent on InterBox Core.
 * 
 * @version 0.5.20130110
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @license MIT License
 * @copyright &copy; 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.framework
 */
//-----------------------------------------------------------
// paths
//-----------------------------------------------------------
/**
 * gets a relative path to a resource file in the system level
 *
 * @param string $resname   a resource file name in the specified path
 * @param string $path      a path relative to the configured system root
 * For example,
 * if /var/www is the configured system root
 * and the resource file is /var/www/scripts/calendar.js,
 * the path is "scripts".
 * @return string   gives a path to the resource file relative to the
 * configured system root, so it can be used in html; may give an empty string
 * indicating that the resource file does not exist
 */
function GetSysResPath($resname, $path) {
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = IBC1_SYSTEM_ROOT;
    $sysrespath = $path . '/' . $resname;
    if (is_file($syspath . $sysrespath))
        return $sysrespath;
    return '';
}

/**
 * gets a relative path to a resource file in the theme level
 *
 * @param string $resname   a resource file name
 * @param string $restype   images, stylesheets, scripts
 * @param int $themeid  optional
 * @return string
 */
function GetThemeResPath($resname, $restype, $themeid = NULL) {
    //set default theme id
    if (empty($themeid))
        $themeid = GetThemeID();

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = IBC1_SYSTEM_ROOT;

    //get relative path
    $sysrespath = $restype . '/' . $resname;
    $themerespath = 'themes/' . $themeid . '/' . $sysrespath;

    //validate
    if (is_file($syspath . $themerespath))
        return $themerespath;
    if (is_file($syspath . $sysrespath))
        return $sysrespath;

    //not found
    return '';
}

/**
 * gets an absolute path to a directory where cache files are stored
 *
 * @param bool $makedir  optional
 * @param int $themeid  optional
 * @param string $lang   optional
 */
function GetCachePath($makedir = FALSE, $themeid = NULL, $lang = NULL) {
    //set default values
    if (empty($lang))
        $lang = GetLang();
    $lang = strtolower($lang);
    if (empty($themeid))
        $themeid = GetThemeID();
    $themeid = intval($themeid);

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = IBC1_SYSTEM_ROOT;

    //get relative path
    $cachepath = 'cache/' . $themeid . '/' . $lang;

    //validate
    if (is_dir($syspath . $cachepath)) {
        return $syspath . $cachepath;
    } else if ($makedir) {
        if (!is_dir($syspath . 'cache/' . $themeid))
            mkdir($syspath . 'cache/' . $themeid);
        if (mkdir($syspath . $cachepath))
            return $syspath . $cachepath;
    }

    //not found
    return '';
}

/**
 * gets an absolute path to a directory where template files are stored
 *
 * @param string $filename
 * @param string $classname
 * @param int $themeid
 * @param string $lang
 * @return string
 */
function GetTplPath($filename, $classname = NULL, $themeid = NULL, $lang = NULL) {

    if (empty($themeid))
        $themeid = GetThemeID();
    $themeid = intval($themeid);

    //get system path
    $syspath = '';
    if (defined('IBC1_SYSTEM_ROOT'))
        $syspath = IBC1_SYSTEM_ROOT;

    //get relative path
    $sysrespath = 'templates/';
    $themerespath = 'themes/' . $themeid . '/templates/';

    if (!empty($classname))
        $classname.='/';
    else
        $classname = '';

    if ($lang == NULL)
        $lang = GetLang();
    else
        $lang = strtolower($lang);

    while (TRUE) {
        if ($lang != 'neutral') {
            $tplpath = $syspath . $themerespath . $classname . $lang . '/' . $filename;
            if (is_file($tplpath)) {
                return $tplpath;
            }
            $tplpath = $syspath . $sysrespath . $classname . $lang . '/' . $filename;
            if (is_file($tplpath)) {
                return $tplpath;
            }
        }
        if ($lang != IBC1_DEFAULT_LANGUAGE) {
            //neutral
            $tplpath = $syspath . $themerespath . $classname . $filename;
            if (is_file($tplpath)) {
                return $tplpath;
            }
            $tplpath = $syspath . $sysrespath . $classname . $filename;
            if (is_file($tplpath)) {
                return $tplpath;
            }
            $lang = IBC1_DEFAULT_LANGUAGE;
        } else {
            //not found
            return '';
        }
    }
}

LoadIBC1Class('ICacheReader', 'cache');
LoadIBC1Class('PHPCacheReader', 'cache.phpcache');

//-----------------------------------------------------------
// themes
//-----------------------------------------------------------
/**
 * gets theme id.
 *
 * It can be user preferred one or the default one.
 * @return int
 */
function GetThemeID() {
    $key = IBC1_PREFIX . '_ThemeID';

    if (isset($GLOBALS[$key]))
        return $GLOBALS[$key];

    $id = strCookie('ThemeID');

    if (empty($id))
        $GLOBALS[$key] = 1; //default & preserved theme id
    else
        $GLOBALS[$key] = intval($id);

    return $GLOBALS[$key];
}

/**
 * sets an user preferred theme.
 * 
 * @param int $id 
 */
function SetThemeID($id) {
    $key = IBC1_PREFIX . '_ThemeID';
    $id = intval($id);
    $GLOBALS[$key] = $id;
    setcookie($key, $id, time() + 7 * 24 * 60 * 60);
}

/**
 * gets all themes.
 * 
 * Theme information is stored in "themes.conf.php" in the directory 
 * of the web system named "themes".
 * @return array
 * <code>
 * array(
 *      1=>'theme name',// the default theme
 *      2=>'theme 2',// another theme
 *      //...
 * )
 * </code> 
 */
function GetThemes() {
    return include GetSysResPath('themes.conf.php', 'themes');
}

//-----------------------------------------------------------
// languages
//-----------------------------------------------------------
/**
 * gets language code according to http request and the system default setting.
 *
 * @return string
 */
function GetLang() {
    $key = IBC1_PREFIX . '_Language';

    //not first time
    if (isset($GLOBALS[$key]))
        return $GLOBALS[$key];

    //by preference
    $lang = strtolower(strCookie('Language'));
    if ($lang != '' && is_dir('lang/' . $lang)) {
        $GLOBALS[$key] = $lang;
        return $lang;
    }

    //by browser
    $l = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $l = explode(',', $l[0]);
    foreach ($l as $lang) {
        $lang = strtolower($lang);
        if (is_dir('lang/' . $lang)) {
            $GLOBALS[$key] = $lang;
            return $lang;
        }
        $pos = strpos($lang, '-');
        if ($pos > 0) {
            //e.g. zh-cn
            $lang = substr($lang, 0, $pos);
            if (is_dir('lang/' . $lang)) {
                $GLOBALS[$key] = $lang;
                return $lang;
            }
        }
    }

    //default
    $GLOBALS[$key] = strtolower(IBC1_DEFAULT_LANGUAGE);
    return $GLOBALS[$key];
}

/**
 * sets an user preferred language.
 * 
 * @param string $lang e.g. en, or zh-cn
 */
function SetLang($lang) {
    if (is_dir('lang/' . $lang)) {
        $key = IBC1_PREFIX . '_Language';
        $GLOBALS[$key] = $lang;
        setcookie($key, $lang, time() + 7 * 24 * 60 * 60);
    }
}

/**
 * gets all languages.
 * 
 * Language information is stored in "languages.conf.php" in the directory 
 * of the web system named "lang".
 * @return array
 * <code>
 * array(
 *      'en'=>'English',
 *      'zh-cn'=>'Simplified Chinese',
 *      //...
 * )
 * </code> 
 */
function GetLanguages() {
    return include GetSysResPath('languages.conf.php', 'lang');
}

/**
 * reads words in a certain language with a given key
 *
 * @param string $key   a short word that identifies a longer expression
 * in some language
 * @param string $group optional, designed to reduce loading unused data
 * @return string
 */
function GetLangData($key, $group = NULL) {

    // read language code
    $lang = &$GLOBALS[IBC1_PREFIX . '_Language'];
    if (!isset($lang))
        $lang = GetLang();

    // data group
    if (empty($group))
        $group = $lang;
    else
        $group = $lang . '_' . $group;

    // load a reader for the group if not existing
    $reader = &$GLOBALS[IBC1_PREFIX . '_LangDataReader'];
    if (!isset($reader) || !isset($reader[$group])) {
        $reader[$group] = new PHPCacheReader("lang/$lang/$group.lang.php", $group);
    }
    // read data
    return $reader[$group]->GetValue($key);
}

//-----------------------------------------------------------
// configurations
//-----------------------------------------------------------
/**
 * reads a value associated with the given key in the config file
 *
 * @param string $key
 * @param string $group optional, designed to reduce loading unused data
 * @return mixed
 */
function GetConfigValue($key, $group = NULL) {
    if (empty($group))
        $group = 'settings'; //default group

    $reader = &$GLOBALS[IBC1_PREFIX . '_ConfigReader'];
    if (!isset($reader) || !isset($reader[$group])) {
        $reader[$group] = new PHPCacheReader("conf/$group.conf.php", $group);
    }
    return $reader[$group]->GetValue($key);
}

//-----------------------------------------------------------
//template processing
//-----------------------------------------------------------
/**
 * mappings from formatting types defined in template fields to 
 * formatting functions
 * 
 * @global array $GLOBALS['IBC1_TEMPLATE_FORMATTING']
 * @name $IBC1_TEMPLATE_FORMATTING 
 */
$GLOBALS[IBC1_PREFIX . '_TEMPLATE_FORMATTING'] = array(
    'html' => 'filterhtml',
    'text' => 'htmlspecialchars', //TODO nl2br or not
    'jsstr' => 'toScriptString',
    'urlparam' => 'urlencode',
    'int' => 'intval',
    'float' => 'floatval'
);

/**
 * reads a template file and gets content of it
 *
 * @param string $tplname
 * @param string $classname
 * @param int $themeid
 * @param string $lang
 * @return string
 */
function GetTemplate($tplname, $classname = NULL, $themeid = NULL, $lang = NULL) {
    $path = GetTplPath($tplname . '.tpl', $classname, $themeid, $lang);
    if ($path == '')
        return '';
    return file_get_contents($path);
}

/**
 * reads a template, passes parameters to it and generates HTML
 *
 * @param string $tplname
 * @param array $vars
 * @param string $classname
 * @param int $themeid
 * @param string $lang
 * @return string
 * @see GetTemplate()
 * @see Tpl2HTML()
 */
function TransformTpl($tplname, $vars, $classname = NULL, $themeid = NULL, $lang = NULL) {
    $tpl = GetTemplate($tplname, $classname, $themeid, $lang);

    return Tpl2HTML($tpl, $vars);
}

/**
 * assigns parameters to the template and generates HTML
 *
 * @see $GLOBALS['IBC1_TEMPLATE_FORMATTING']
 * @param string $tpl   content of a template
 * @param array $vars   variables to be assigned
 * <code>
 * array(
 *     [variable1 name]=>[variable1 value],
 *     [variable2 name]=>[variable2 value],
 *     ...
 * )
 * </code>
 * @return string
 */
function Tpl2HTML($tpl, $vars) {
    foreach ($vars as $varname => $varvalue) {
        $pos = strpos($varname, '_');
        if ($pos) {
            $func = substr($varname, 0, $pos);
            $funclist = &$GLOBALS[IBC1_PREFIX . '_TEMPLATE_FORMATTING'];
            if (isset($funclist[$func])) {
                $varvalue = call_user_func($funclist[$func], $varvalue);
            }
        }
        $varvalue = str_replace('\\', '\\\\', $varvalue);
        $varvalue = str_replace('"', '\\"', $varvalue);
        $varvalue = str_replace('$', '\\$', $varvalue);
        eval("\$$varname=\"$varvalue\";");
    }
    $tpl = str_replace('\\', '\\\\', $tpl);
    $tpl = str_replace('"', '\\"', $tpl);
    eval("\$tpl=\"$tpl\";");
    return $tpl;
}

//-----------------------------------------------------------
// caching related
//-----------------------------------------------------------
/**
 * checks data version with the current up-to-date version stored somewhere
 * in the system for the necessity to regenerate the data to be cached
 *
 * @param int $dataversion
 * @param int $current
 * @return boolean
 */
function IsCachedDataOld($dataversion, $current) {
    if ($dataversion < 1)
        return FALSE;
    return $current > $dataversion;
}

/**
 * designed for <code>$cacheVersion</code> in Box and BoxGroup
 *
 * It is useful when there are a couple of versions that affect the views
 * created by a Box or a BoxGroup.
 * For example, in a box view, there is a list of titles whose data source
 * has a version A, whereas a setting data item with a version of B controls
 * the number of the titles in the list. Therefore, the
 * <code>$cacheVersion</code> for this box view should be
 * <code>AddVersions(A,B)</code>.
 * @param int $version1
 * @param int $version2
 * @return int
 */
function AddVersions($version1, $version2) {
    if ($version1 > $version2)
        return $version1;
    return $version2;
}

/**
 * designed for <code>$cacheGroup</code> and <code>$cacheKey</code>
 * in Box and BoxGroup
 *
 * @param string $name      a name for cached data group or key and simply
 * for basic identification
 * @param array $factors    optional, for further identification, meaning
 * factors that affect the content, which should be cached differently
 * For example, in a paged list, pages should be cached separately and one of
 * the so-called factors is its page number.
 * Note that in this version, the sequence of factors matters. So, be sure
 * that it is consistent.
 * @return string
 */
function GenerateCacheId($name, $factors = NULL) {
    //TODO sequence of factors
    if (!empty($factors)) {
        $name.=implode('', $factors);
//        foreach ($factors as $f) {
//            $name.=$f;
//        }
    }
    //SOLUTION 1:
    //    $name = str_replace('/', '_', $name);
    //    $name = str_replace('\', '_', $name);
    //    $name = str_replace(':', '_', $name);
    //    return $name;
    //SOLUTION 2:
    //    return md5($name);
    //SOLUTION 3:
    return urlencode($name);
}

//-----------------------------------------------------------
//the Page-Process-Box model
//-----------------------------------------------------------

/**
 * a generic page model, based on a simple php string template
 *
 * @version 0.9.20121122
 */
abstract class PageModel {

    /**
     * name of page template
     *
     * @var string
     */
    private $_pagetpl = '';
    private $_classname;
    private $_title = '';
    private $_keywords;
    private $_description;
    private $_meta = '';
    private $_css = '';
    private $_cssfile = '';
    private $_js = '';
    private $_jsfile = '';
    private $_icon = '';
    private $_regions = array();

    /**
     * constructor
     *
     * @param string $pagetpl   name of page template
     * @see $_pagetpl
     */
    function __construct($pagetpl, $classname = NULL) {
        $this->_pagetpl = $pagetpl;
        $this->_classname = empty($classname) ? __CLASS__ : $classname;
        LoadIBC1Class('WordList', 'util');
        $this->_keywords = new WordList();
        $this->_description = new WordList();
        $this->Initialize();
    }

    /**
     * automatically called in the end of constructing process
     *
     *  e.g. start timer
     */
    abstract protected function Initialize();

    /**
     * invoke functions requested by the client and registered in the
     *  config parameter; add box views specified as default views in
     *  the config parameter or as output views from processes into
     *  the page
     *
     * manually called before outputing HTML
     * @param array $config
     * <code>
     * array(
     *      'definitions'=>array(
     *          'module'=>'',
     *          'function'=>''
     *      ),
     *      'box'=>array([box name],[params]),
     *      'boxes'=>array(
     *          array([box name],[params]),
     *          ...
     *      ),
     *      'functions'=>array(
     *          [function name]=>array([process name],[params]),
     *          ...
     *      ),
     *      'modules'=>array(
     *          [module name]=>array(
     *              'box'=>array([box name],[params]),
     *              'boxes'=>array(
     *                  array([box name],[params]),
     *                  ...
     *              ),
     *              'functions'=>array(
     *                  [function name]=>array([process name],[params]),
     *                  ...
     *              )
     *          ),
     *          ...
     *      )
     * )
     * </code>
     */
    final public function Prepare(array $config) {

        //default parameter names for module & function
        $module = 'module';
        $function = 'function';

        //custom parameter names for module & function
        if (isset($config['definitions'])) {
            $defconf = &$config['definitions'];
            if (isset($defconf['module']))
                $module = $defconf['module'];
            if (isset($defconf['function']))
                $function = $defconf['function'];
        }

        //dynamically load and invoke functions
        // either in or out of specified modules
        $module_name = strGet($module);
        $function_name = strGet($function);
        if (!empty($module_name) && isset($config['modules'])) {

            //locate a module with a recursive approach
            //?module=[module name]
            $modconf = &$config['modules'];
            if (isset($modconf[$module_name])) {
                return $this->Prepare($modconf[$module_name]);
            }
        } else if (!empty($function_name) && isset($config['functions'])) {

            //?function=[function name]
            //?module=[module name]&function=[function name]
            $funconf = &$config['functions'];
            if (isset($funconf[$function_name])) {

                $proconf = &$funconf[$function_name];
                //a process should be done only once
                //require GetSysResPath($proconf[0], $proconf[1]);#fixed package
                require GetSysResPath($proconf[0] . '.class.php', 'modules/processes');

                //$proc = new $proconf[0]($proconf[2]);#delete package field
                $proc = new $proconf[0]($proconf[1]);
                if ($proc->Process()) {

                    //show output of the function
                    //require GetSysResPath($proc->output_box, $proc->output_box_pkg);#fixed package
                    require_once GetSysResPath($proc->output_box . '.class.php', 'modules/boxes');
                    $this->AddBox(new $proc->output_box($proc->output_box_params), $module_name);

                    return TRUE;
                }

                return FALSE; //silent & no output
            }
        }

        //a default view if there's no function invoked
        if (isset($config['box'])) {

            //a single box view
            //require GetSysResPath($config['box'][0], $config['box'][1]);#fixed package
            require_once GetSysResPath($config['box'][0] . '.class.php', 'modules/boxes');
            //$this->AddBox(new $config['box'][0]($config['box'][2]));#delete package field
            $this->AddBox(new $config['box'][0]($config['box'][1]), $module_name);

            return TRUE;
        } else if (isset($config['boxes'])) {

            //an array of boxes
            foreach ($config['boxes'] as $b) {
                //require GetSysResPath($b[0], $b[1]);#fixed package
                require_once GetSysResPath($b[0] . '.class.php', 'modules/boxes');
                //$this->AddBox(new $b[0]($b[2]));#delete package field
                $this->AddBox(new $b[0]($b[1]), $module_name);
            }

            return TRUE;
        }
        return FALSE;
    }

    /**
     * automatically invoked when outputing HTML
     *
     * e.g. set page header/footer, stop timer
     */
    abstract protected function Finalize();

    /**
     * add a box to a region defined by the box itself
     *
     * @param BoxModel $box
     * @param string $module
     */
    final public function AddBox(BoxModel $box, $module = '') {
        $box->module = $module;
        $box->Before($this);
        $html = $box->GetHTML();
        $r = $box->region;
        if (isset($this->_regions[$r]))
            $this->_regions[$r].= $html;
        else
            $this->_regions[$r] = $html;
        $box->After($this);
    }

    /**
     * set title for the page
     *
     * @param string $title
     */
    public function SetTitle($title) {
        $this->_title = htmlspecialchars($title);
    }

    /**
     * add keywords
     *
     * @param string $keywords
     */
    public function AddKeywords($keywords) {
        $this->_keywords->AddWords($keywords);
    }

    /**
     * append a string to description
     *
     * @param string $desc
     */
    public function AppendDescription($desc) {
        $this->_description->AddWords($desc);
    }

    /**
     * add extra &lt;meta&gt; information
     *
     * @param string $name
     * @param string $content
     */
    public function AddMeta($name, $content) {
        $name = htmlspecialchars($name);
        $content = htmlspecialchars($content);
        $this->_meta.="<meta content=\"$content\" name=\"$name\" />\n";
    }

    /**
     * append CSS code fragment to &lt;head&gt;
     *
     * @param string $css
     */
    public function AppendCSS($css) {
        $this->_css.=$css;
    }

    /**
     * add a CSS file to &lt;head&gt;
     *
     * @param string $cssfile
     * @param int $mode
     * <ul>
     * <li>mode=0, external => module=URL that locates the js file</li>
     * <li>mode=1, system level => module=name of the js file</li>
     * <li>mode=2, theme level => module=name of the js file</li>
     * </ul>
     */
    public function AddCSSFile($cssfile, $mode = 0) {
        //TODO prevent repetition
        switch ($mode) {
            case 1:
                $cssfile = GetSysResPath($cssfile, 'stylesheets');
                break;
            case 2:
                $cssfile = GetThemeResPath($cssfile, 'stylesheets');
                break;
        }
        if (!empty($cssfile)) {
            $this->_cssfile .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$cssfile}\" />\n";
        }
    }

    /**
     * add an array of CSS files to &lt;head&gt;
     *
     * @param array $cssfiles
     * array(
     *      array([cssfile],[mode]),
     *      ...
     * )
     * @see AddCSSFile()
     */
    public function AddCSSFiles(array $cssfiles) {
        if (!empty($cssfiles)) {
            foreach ($cssfiles as $css) {
                $this->AddCSSFile($css[0], $css[1]);
            }
        }
    }

    /**
     * append JavaScript code fragment to &lt;head&gt;
     *
     * @param string $js
     */
    public function AppendJS($js) {
        $this->_js.=$js;
    }

    /**
     * add a JavaScript file to &lt;head&gt
     *
     * @param string $jsfile
     * @param int $mode
     * @see AddCSSFile()
     */
    public function AddJSFile($jsfile, $mode = 0) {
        //TODO prevent repetition
        switch ($mode) {
            case 1:
                $jsfile = GetSysResPath($jsfile, 'scripts');
                break;
            case 2:
                $jsfile = GetThemeResPath($jsfile, 'scripts');
                break;
        }
        if (!empty($jsfile)) {
            $this->_jsfile .= "<script language=\"javascript\" type=\"text/javascript\" src=\"{$jsfile}\"></script>\n";
        }
    }

    /**
     * add an array of JavaScript files to &lt;head&gt
     *
     * @param array $jsfiles
     * array(
     *      array([jsfile],[mode]),
     *      ...
     * )
     * @see AddJSFile()
     */
    public function AddJSFiles(array $jsfiles) {
        if (!empty($jsfiles)) {
            foreach ($jsfiles as $js) {
                $this->AddJSFile($js[0], $js[1]);
            }
        }
    }

    /**
     * set an image as an icon
     * @param stirng $iconfile
     */
    public function SetIcon($iconfile, $type = '') {
        if ($type == '')
            $type = GetFileExt($iconfile);
        switch (strtolower($type)) {
            case 'ico':
                $mime = 'image/x-icon';
                break;
            case 'jpeg':
            case 'jpg':
                $mime = 'image/jpeg';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'bmp':
                $mime = 'image/bmp';
                break;
            default:
                $mime = $type;
        }
        $this->_icon = "<link rel=\"shortcut icon\" type=\"{$mime}\" href=\"{$iconfile}\" />\n";
    }

    final public function SetField($fieldname, $value) {
        $this->_regions[$fieldname] = $value;
    }

    final public function GetHTML() {
        //head BEGIN
        $head = '';
        //icon
        if (!empty($this->_icon)) {
            $head.=$this->_icon;
        }
        //css
        if (!empty($this->_cssfile)) {
            $head.=$this->_cssfile;
        }
        if (!empty($this->_css)) {
            $head.="<style>\n<!--\n{$this->_css}\n-->\n</style>\n";
        }
        //js
        if (!empty($this->_jsfile)) {
            $head.=$this->_jsfile;
        }
        if (!empty($this->_js)) {
            $head.="<script language=\"javascript\" type=\"text/javascript\">\n//<![CDATA[\n{$this->_js}\n//]]>\n</script>\n";
        }
        //meta
        $head.=$this->_meta;
        //head END
        //output
        $this->_regions['Keywords'] = $this->_keywords->GetWords();
        $this->_regions['Description'] = $this->_description->GetWords();
        $this->_regions['Title'] = $this->_title;
        $this->_regions['Head'] = $head;
        $this->Finalize();
        return TransformTpl($this->_pagetpl, $this->_regions, $this->_classname);
    }

    final public function Show() {
        echo $this->GetHTML();
    }

}

/**
 * an abstract process for the Page-Process-Box model
 *
 * @version 0.2.20121122
 */
abstract class ProcessModel {

    /**
     * name of a Box as output
     * @var string
     */
    var $output_box;

    /*
     * package name of the Box
     * @var string
     */
    //var $output_box_pkg;

    /**
     * parameters for constructing the Box
     * @var array
     */
    var $output_box_params;

    /**
     * @return bool
     * - TRUE  : output is prepared as parameters
     * - FALSE : silently ends the process without any output
     */
    abstract public function Process();

    public function Output($box, array $params) {
        $this->output_box = $box;
        $this->output_box_params = $params;
    }

}

/**
 * a box container in the Page-Process-Box model
 *
 * @version 0.7.20130109
 */
abstract class BoxModel {

    const STATUS_NORMAL = 0;
    const STATUS_USECACHE = 1;
    const STATUS_FORWARD = 2;
    const STATUS_HIDDEN = 3;

    /**
     * 0 - normal
     * 1 - use cache
     * 2 - forward
     * 3 - hidden
     * @var int
     */
    var $status;

    /**
     * name of the region in the template where the box is displayed
     * @var string 
     */
    var $region;

    /**
     * name of the module where it is deployed by the page
     * @var string 
     */
    var $module;

    /**
     * template name, 
     * e.g. templates/[class name]/[lang]/{{template name}}.tpl
     * @var string 
     */
    private $_tplName;

    /**
     * name of the current class, 
     * e.g. templates/{{class name}}/[lang]/[template name].tpl
     * @var string 
     */
    private $_classname;

    /**
     * name of a Box to be forwarded
     * @var string
     */
    private $_forwardbox;

    /**
     * parameters for constructing the Box
     * @var array
     */
    private $_forwardbox_params;
    private $_extrafields = array();
    private $_cachereader = NULL;

    /**
     * path where cache files are stored
     * @var string 
     */
    protected $cachePath;
    protected $cacheTimeout;
    protected $cacheGroup;
    protected $cacheKey;
    protected $cacheVersion;
    protected $cacheRandFactor;
    protected $contentFieldName;

    function __construct($region, $tpl, $classname = NULL) {
        $this->status = BoxModel::STATUS_NORMAL;
        $this->region = $region;
        $this->_tplName = $tpl;
        $this->_classname = empty($classname) ? __CLASS__ : $classname;
        $this->cachePath = '';
        $this->cacheGroup = '';
        $this->cacheKey = '';
        $this->cacheTimeout = 0;
        $this->cacheVersion = 0;
        $this->cacheRandFactor = 1;
        $this->contentFieldName = 'Content';
    }

    /**
     * change box status to hidden 
     */
    final protected function Hide() {
        $this->status = BoxModel::STATUS_HIDDEN;
    }

    /**
     * set the next box to be forwarded
     * 
     * @param string $box
     * @param array $params 
     */
    final protected function Forward($box, $params) {
        $this->status = BoxModel::STATUS_FORWARD;
        $this->_forwardbox = $box;
        $this->_forwardbox_params = $params;
    }

    /**
     * inform that loading content failed so as to use cached data instead
     */
    final protected function UseCache() {
        $this->status = BoxModel::STATUS_USECACHE;
    }

    /**
     *
     * @return null|ICacheEditor 
     */
    protected function LoadCacheWriter() {
        if (empty($this->cacheGroup))
            return NULL;
        LoadIBC1Class('ICacheEditor', 'cache');
        LoadIBC1Class('PHPCacheEditor', 'cache.phpcache');
        return new PHPCacheEditor($this->cachePath, $this->cacheGroup);
    }

    /**
     *
     * @return null|ICacheReader 
     */
    protected function LoadCacheReader() {
        if (empty($this->cacheGroup))
            return NULL;
        //LoadIBC1Class('ICacheReader', 'cache');
        //LoadIBC1Class('PHPCacheReader', 'cache.phpcache');
        return new PHPCacheReader($this->cachePath, $this->cacheGroup);
    }

    /**
     * @return string 
     */
    private function LoadForwardedContent() {
        if (empty($this->_forwardbox))
            return '';
        require_once GetSysResPath($this->_forwardbox . '.class.php', 'modules/boxes');
        $box = new $this->_forwardbox($this->_forwardbox_params);
        return $box->GetHTML();
    }

    /**
     * @return string 
     */
    abstract protected function LoadContent();

    abstract public function Before($page);

    abstract public function After($page);

    final public function SetField($fieldname, $value) {
        $this->_extrafields[$fieldname] = $value;
    }

    final public function GetRefreshedHTML() {

        $html = $this->LoadContent();

        // during content loading, status can be changed 
        switch ($this->status) {
            case BoxModel::STATUS_NORMAL:

                // fill content in the template
                if (!empty($this->_tplName)) {
                    $this->_extrafields[$this->contentFieldName] = $html;
                    $html = TransformTpl(
                            $this->_tplName, $this->_extrafields, $this->_classname
                    );
                }

                // write into cache storage
                try {
                    $ce = $this->LoadCacheWriter();
                    if (!empty($ce)) {
                        $ce->SetValue(
                                $this->cacheKey, $html, $this->cacheTimeout, $this->cacheVersion > 0
                        );
                        $ce->Save();
                    }
                } catch (Exception $ex) {
                    
                }

                break;

            case BoxModel::STATUS_USECACHE:

                $cr = $this->_cachereader;
                if (!empty($cr)) {
                    $cr->SetRefreshFunction(NULL);
                    $html = $cr->GetValue($this->cacheKey, 0, 0);
                }

                break;

            case BoxModel::STATUS_FORWARD:

                $html = $this->LoadForwardedContent();
                break;

            default: // Box::STATUS_HIDDEN

                $html = '';

                break;
        }

        return $html;
    }

    public function GetHTML() {

        //status may change in Before()
        switch ($this->status) {
            case BoxModel::STATUS_FORWARD:

                return $this->LoadForwardedContent();

            case BoxModel::STATUS_HIDDEN:

                return '';

            default:

                //read from cache storage
                $this->_cachereader = $this->LoadCacheReader();
                $cr = $this->_cachereader;
                if (!empty($cr)) {
                    if ($this->status == BoxModel::STATUS_USECACHE) {
                        $cr->SetRefreshFunction(NULL);
                        return $cr->GetValue($this->cacheKey, 0, 0);
                    } else {
                        $cr->SetRefreshFunction(array($this, 'GetRefreshedHTML'));
                        return $cr->GetValue($this->cacheKey, $this->cacheVersion, $this->cacheRandFactor);
                    }
                }
                return $this->GetRefreshedHTML();
        }
    }

}
