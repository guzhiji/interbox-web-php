<?php

/**
 * an abstract web ui model, based on a simple php string template
 * @version 0.4.20111223
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.uimodel
 */
abstract class UIModel {

    /**
     * the directory path to the needed template file
     * @var string 
     */
    protected $_path;

    /**
     * constructor
     * @param string $path  optional, the directory path to the needed template file
     * if not given, the constant IBC1_UIMODEL_PATH has to be defined
     */
    function __construct($path="") {
        if ($path != "") {
            $this->SetTplPath($path);
        } else if (defined("IBC1_UIMODEL_PATH")) {
            $this->SetTplPath(IBC1_UIMODEL_PATH);
        }
    }

    /**
     * set the directory path to the needed template file
     * @param string $path 
     */
    private function SetTplPath($path) {
        if (is_file($path)) {
            $this->_path = FormatPath($path);
        }
    }

    /**
     * output html
     * @return string
     */
    abstract public function GetHTML();

    /**
     * read template file
     * @param string $tplname
     * @return string 
     */
    protected function GetTemplate($tplname) {
        if (!is_file($this->_path . $tplname . ".tpl"))
            return "";
        return file_get_contents($this->_path . $tplname);
    }

    /**
     * read template, pass parameters to it and generate HTML
     * @param string $tplname
     * @param array $vars
     * @param string $tplclass
     * @return string 
     * @see GetTemplate()
     * @see Tpl2HTML()
     */
    protected function TransformTpl($tplname, $vars, $tplclass="") {
        $tpl = $this->GetTemplate($tplname);
        if ($tplclass != "") {
            return $this->Tpl2HTML($tpl, $vars, $tplclass);
        } else {
            return $this->Tpl2HTML($tpl, $vars);
        }
    }

    /**
     * pass parameters to the template and generate HTML
     * @param string $tpl   content of a template
     * @param array $vars   variables to be assigned
     * <code>
     * array(
     *     [variable1 name]=>[variable1 value],
     *     [variable2 name]=>[variable2 value],
     *     ...
     * )
     * </code>
     * @param string $tplclass  optional, the prefix of variable names in the template
     * @return string 
     */
    protected function Tpl2HTML($tpl, $vars, $tplclass="") {
        foreach ($vars as $varname => $varvalue) {
            $varvalue = str_replace("\\", "\\\\", $varvalue);
            $varvalue = str_replace("\"", "\\\"", $varvalue);
            $varvalue = str_replace("\$", "\\\$", $varvalue);
            if ($tplclass != "") {
                $varname = $tplclass . "_" . $varname;
            }
            eval("\$$varname=\"$varvalue\";");
        }
        $tpl = str_replace("\\", "\\\\", $tpl);
        $tpl = str_replace("\"", "\\\"", $tpl);
        eval("\$tpl=\"$tpl\";");
        return $tpl;
    }

}

?>