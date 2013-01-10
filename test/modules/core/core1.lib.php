<?php

/**
 * the main library for InterBox Core 1
 * 
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright 2010-2013 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core
 */
define('IBC1_DATATYPE_INTEGER', 0);
define('IBC1_DATATYPE_DECIMAL', 1);
define('IBC1_DATATYPE_PLAINTEXT', 2); //ensure the first string-type
define('IBC1_DATATYPE_RICHTEXT', 3);
define('IBC1_DATATYPE_TEMPLATE', 4);
define('IBC1_DATATYPE_DATETIME', 5);
define('IBC1_DATATYPE_DATE', 6);
define('IBC1_DATATYPE_TIME', 7);
define('IBC1_DATATYPE_URL', 8);
define('IBC1_DATATYPE_EMAIL', 9);
define('IBC1_DATATYPE_PWD', 10);
define('IBC1_DATATYPE_WORDLIST', 11);
define('IBC1_DATATYPE_BINARY', 12);
define('IBC1_DATATYPE_EXPRESSION', 13);

define('IBC1_LOGICAL_AND', 0);
define('IBC1_LOGICAL_OR', 1);

define('IBC1_ORDER_ASC', 0);
define('IBC1_ORDER_DESC', 1);

define('IBC1_VALUEMODE_VALUEONLY', 0);
define('IBC1_VALUEMODE_TYPEONLY', 1);
define('IBC1_VALUEMODE_ALL', 2);

define('IBC1_DEFAULT_DBDRIVER', 'mysqli');
define('IBC1_DEFAULT_CACHE', 'phpcache');
define('IBC1_DEFAULT_LANGUAGE', 'zh-cn');

define('IBC1_ENCODING', 'UTF-8');
define('IBC1_PREFIX', 'ibc1');

//define('IBC1_MODE_DEV', TRUE);
//define('IBC1_SYSTEM_ROOT', 'C:/Users/guzhiji/wamp/www/DigitalBox_3/src/'); //slash at the end
//define('IBC1_SYSTEM_ROOT', '/var/www/DigitalBox_3/src/'); //slash at the end

function FormatPath($path, $filename = '') {
    $path = str_replace('\\', '/', $path); //for windows
    if (substr($path, -1) != '/')
        $path.='/';
    return $path . $filename;
}

function LoadIBC1File($filename, $package = '') {
    $path = FormatPath(dirname(__FILE__));
    if ($package != '')
        $path.=str_replace('.', '/', $package) . '/';
    $path.=$filename;
    require_once($path);
}

function LoadIBC1Class($classname, $package = '') {
    LoadIBC1File($classname . '.class.php', $package);
}

function LoadIBC1Lib($libname, $package = '') {
    LoadIBC1File($libname . '.lib.php', $package);
}

function toScriptString($str, $isphp = FALSE) {
    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace('"', '\\"', $str);
    if ($isphp)
        $str = str_replace('$', '\\$', $str);
    return "\"$str\"";
}

function filterhtml($html) {
    if (!isset($GLOBALS['IBC1_HTMLFILTER'])) {
        LoadIBC1Class('HTMLFilter', 'util');
        $GLOBALS['IBC1_HTMLFILTER'] = new HTMLFilter();
    }
    return $GLOBALS['IBC1_HTMLFILTER']->filter($html);
}

function ValidateURL($url) {
    return (!!preg_match('/^(\w+):\/\/([^/:]+)(:\d*)?([^# ]*)$/i', $url));
}

function ValidateEMail($email) {
    return(!!preg_match('/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$/i', $email));
}

function ValidateUID($uid) {
    return(!!preg_match('/^[0-9a-z_]{3,256}$/i', $uid));
}

function ValidatePWD($pwd) {
    return (!!preg_match('/^[0-9a-z]{6,}$/i', $pwd));
}

function ValidateFieldName($fieldname) {
    return (!!preg_match('/^[a-z_][0-9a-z_]{0,63}$/i', $fieldname));
}

function ValidateTableName($tablename) {
    return (!!preg_match('/^[a-z_][0-9a-z_]{0,63}$/i', $tablename));
}

function ValidateServiceName($fieldname) {
    return (!!preg_match('/^[0-9a-z_]{0,32}$/i', $fieldname));
}

function PageRedirect($page) {
    $page = str_replace('\\', '/', $page);
    $url = $_SERVER['SCRIPT_NAME'];
    $url = substr($url, 0, strrpos($url, '/'));
    while (substr($page, 0, 3) == '../') {
        if (!strrpos($url, '/'))
            break;
        $url = substr($url, 0, strrpos($url, '/'));
        $page = substr($page, 3, strlen($page) - 3);
    }
    if ($url == '')
        $url = '/';
    if ($page != '')
        if (substr($page, 0, 1) != '/')
            $page = '/' . $page;
    $url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $url . $page;
    header('Location: ' . $url);
    exit();
}

/*
  another sulution:
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $extra = 'mypage.php';
  header("Location: http://$host$uri/$extra");
  exit;
 */

//------------------------------------------------------------------
function PageCount($totalrecord, $pagesize) {
    $c = intval($totalrecord) / intval($pagesize);
    $ic = intval($c);
    if ($c > $ic)
        return $ic + 1;
    return $ic;
}

//------------------------------------------------------------------
function PageNumber($n, $maxn) {
    $pagen = intval($n);
    if (strlen($n) > 0) {
        if ($pagen < 1 || $pagen > intval($maxn))
            return 1;
        return $pagen;
    }else
        return 1;
}

//------------------------------------------------------------------
function TrimText($content, $max_len) {
    if (!is_int($max_len)) {
        return $content;
    } else {
        if (function_exists('mb_strlen')) {
            if (mb_strlen($content, IBC1_ENCODING) > $max_len)
                return mb_substr($content, 0, $max_len, IBC1_ENCODING) . '...';
        }else {
            if (strlen($content) > $max_len)
                return substr($content, 0, $max_len) . '...';
        }
        return $content;
    }
}

//------------------------------------------------------------------
function GetFileExt($filename) {
    $a = strpos($filename, '.');
    if ($a > 0)
        return substr($filename, $a + 1, strlen($filename) - $a - 1);
    return '';
}

//------------------------------------------------------------------
function SizeWithUnit($size) {
    if ($size <= 1000) {
        return intval($size) . ' Bytes';
    } else {
        $size = $size / 1024;
        if ($size <= 1000) {
            $unit = 'KB';
        } else {
            $size = $size / 1024;
            if ($size <= 1000) {
                $unit = 'MB';
            } else {
                $size = $size / 1024;
                $unit = 'GB';
            }
        }
        return number_format($size, 3) . ' ' . $unit;
    }
}

function Size2Bytes($size, $unit) {
    $size = doubleval($size);
    switch (strtoupper($unit)) {
        case 'KB':
            $size *= 1024;
        case 'MB':
            $size *= 1024;
        case 'GB':
            $size *= 1024;
    }
    return round($size);
}

//------------------------------------------------------------------
function GetSiteURL() {
    $phpfile = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    return 'http://' . $_SERVER['HTTP_HOST'] . substr($phpfile, 0, strrpos($phpfile, '/') + 1);
}

//------------------------------------------------------------------
function strGet($strname) {
    if (isset($_GET[$strname])) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($_GET[$strname]);
        } else {
            return $_GET[$strname];
        }
    }
    return '';
}

function strPost($strname) {
    if (isset($_POST[$strname])) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($_POST[$strname]);
        } else {
            return $_POST[$strname];
        }
    }
    return '';
}

function strCookie($strname) {
    if (isset($_COOKIE[IBC1_PREFIX . '_' . $strname])) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($_COOKIE[IBC1_PREFIX . '_' . $strname]);
        } else {
            return $_COOKIE[IBC1_PREFIX . '_' . $strname];
        }
    }
    return '';
}

function strSession($strname) {
    if (isset($_SESSION[IBC1_PREFIX . '_' . $strname]))
        return $_SESSION[IBC1_PREFIX . '_' . $strname];
    return '';
}

/**
 * convert a PHP array to a safe string of GET parameters
 * 
 * @param array $params
 * @return string
 */
function queryString($params) {
    $s = '';
    foreach ($params as $key => $value) {
        if (!empty($s))
            $s .= '&';
        $s .= $key . '=' . urlencode($value);
    }
    if (!empty($s))
        return '?' . $s;
    return $s;
}

/**
 * append new GET parameters to existing ones
 * 
 * @param array $params
 * @return string
 */
function queryString_Append($params) {
    $data = array();
    foreach ($_GET as $key => $value)
        $data[$key] = $value;
    foreach ($params as $key => $value)
        $data[$key] = $value;
    return queryString($data);
}

/**
 * read a parameter from defined sources or a default value if missing
 * 
 * if {@code $types} is empty, treat {@code $key} as a global variable;
 * otherwise split it into an array of data sources by "|"
 * and value first found in the data sources will be returned.
 * Data sources:
 * <ul>
 * <li>get - {@code $_GET}</li>
 * <li>post - {@code $_POST}</li>
 * <li>session - {@code $_SESSION}</li>
 * <li>cookie - {@code $_COOKIE}</li>
 * <li>server - {@code $_SERVER}</li>
 * <li>globals - {@code $GLOBALS}</li>
 * <li>name of a global array, otherwise</li>
 * </ul>
 * For example:
 * <code>
 * $id=readParam('get|post', 'id', NULL);
 * $id=isset($_GET['id'])?$_GET['id']:(isset($_POST['id'])?$_POST['id']:NULL);
 * </code>
 * 
 * @param string $types     data sources
 * @param string $key       
 * @param mixed  $default   default value will be returned 
 *                          if {@code $key} is not found in all {@code $types} 
 * @return mixed
 */
function readParam($types, $key, $default = '') {
    if (empty($types)) {
        global $$key;
        if (isset($$key))
            $val = $$key; //a normal variable
        else
            $val = NULL;
    }else {
        $val = NULL;
        $typeArr = explode('|', $types);
        foreach ($typeArr as $type) {
            $t = strtoupper($type);
            switch ($t) {
                case 'GET':
                case 'POST':
                case 'SESSION':
                case 'COOKIE':
                case 'SERVER':
                    $var = '_' . $t;
                    break;
                case 'GLOBALS':
                    $var = $t;
                    break;
                default:
                    $var = $type; //an item in an array
                    break;
            }
            global $$var;
            if (isset($$var)) {
                $arr = $$var;
                if (isset($arr[$key])) {//found
                    $val = $arr[$key];
                    break;
                }
            }
        }
    }
    return empty($val) ? $default : $val;
}
