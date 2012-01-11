<?php

/**
 * a generic model for a table, based on a simple php string template
 * @version 0.4.20120109
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright &copy; 2010-2012 InterBox Core 1.2 for PHP, GuZhiji Studio
 * @package interbox.core.uimodel
 */
class TableModel extends UIModel {

    /**
     * number of columns
     * @var int 
     */
    protected $_coln;

    /**
     * number of rows
     * @var int
     */
    protected $_rown;

    /**
     * template content for an item
     * @var string
     */
    protected $_itemtpl;

    /**
     * template content for a row
     * @var string
     */
    protected $_rowtpl;

    /**
     * template content for the table
     * @var string
     */
    protected $_tabletpl;

    /**
     * number of items added
     * @var int
     */
    protected $_itemcount;

    /**
     * content to fill in the first grid when the table is empty
     * @var string
     */
    protected $_itemempty;

    /**
     * content to fill in the rest empty grids
     * @var string
     */
    protected $_itemrest;
    protected $_classname;

    /**
     * buffer for the table
     * @var string 
     */
    private $_table;

    /**
     * buffer for a new row
     * @var string
     */
    private $_row;

    /**
     * constructor
     * @param string $itemTplName   name of the template for an item
     * @param string $rowTplName    name of the template for a row
     * @param string $tableTplName  name of the template for the table
     * @param int $coln     maximum number of columns
     * @see $_itemtpl
     * @see $_rowtpl
     * @see $_tabletpl
     * @see $_coln
     */
    function __construct($itemTplName, $rowTplName, $tableTplName, $coln) {
        $this->_coln = intval($coln);
        if ($this->_coln < 1)
            $this->_coln = 1;
        $this->_rown = 0;
        $this->_itemcount = 0;
        $this->_table = "";
        $this->_row = "";
        $this->_itemempty = "";
        $this->_itemrest = "";
        $this->_itemtpl = $this->GetTemplate($itemTplName);
        $this->_rowtpl = $this->GetTemplate($rowTplName);
        $this->_tabletpl = $this->GetTemplate($tableTplName);
        $this->_classname = __CLASS__;
    }

    /**
     * set a template for the rest empty grids
     * @param string $tplname   template name
     * @param array $vars   optional
     * @see UIModel::Tpl2HTML()
     * @see $_itemrest
     */
    public function SetRestItem($tplname, array $vars=array()) {
        $this->_itemrest = $this->TransformTpl($tplname, $vars, $this->_classname);
    }

    /**
     * set a template to fill in the first grid when the table is empty
     * @param string $tplname   template name
     * @param array $vars   optional
     * @see UIModel::Tpl2HTML()
     * @see $_itemempty
     */
    public function SetEmptyItem($tplname, array $vars=array()) {
        $this->_itemempty = $this->TransformTpl($tplname, $vars, $this->_classname);
    }

    /**
     * append content of the current row to the table and add 1 to the number of rows
     * @param string $row 
     */
    private function AddRow($row) {
        $this->_table .= $this->Tpl2HTML($this->_rowtpl, array("RowContent" => $row), $this->_classname);
        $this->_rown++;
    }

    /**
     * add an array of items by assigning their associated variables
     *  to the item template
     * @param array $items 
     * <code>
     * array(
     *     array(
     *         [variable1 name]=>[variable1 value],
     *         [variable2 name]=>[variable2 value],
     *         ...
     *     ),
     *     array(
     *         [variable1 name]=>[variable1 value],
     *         [variable2 name]=>[variable2 value],
     *         ...
     *     ),
     *     ...
     * )
     * </code>
     */
    public function AddItems(array $items) {
        foreach ($items as $item) {
            $this->AddItem($item);
        }
    }

    /**
     * assign variables associated with the new item to the item template
     * and append the result to the buffer for a new row
     * @param array $vars   variables associated with the item 
     * @see UIModel::Tpl2HTML()
     */
    public function AddItem(array $vars) {
        $this->_row.=$this->Tpl2HTML($this->_itemtpl, $vars, $this->_classname);
        $this->_itemcount++;
        $m = $this->_itemcount % $this->_coln;
        if ($m == 0) {
            $this->AddRow($this->_row);
            $this->_row = "";
        }
    }

    /**
     * get the number of items added
     * @return int
     */
    public function ItemCount() {
        return $this->_itemcount;
    }

    /**
     * get the number of columns
     * @return int
     */
    public function ColCount() {
        return $this->_coln;
    }

    /**
     * get the number of rows
     * @return int
     */
    public function RowCount() {
        return $this->_row == "" ? $this->_rown : $this->_rown + 1;
    }

    /**
     * get the size of the table, in other words, 
     * the max number of items in the table
     * @return int
     */
    public function Size() {
        return $this->RowCount() * $this->_coln;
    }

    /**
     * clear all items in the table
     */
    public function Clear() {
        $this->_row = "";
        $this->_table = "";
        $this->_itemcount = 0;
        $this->_rown = 0;
    }

    public function GetHTML() {
        if ($this->_itemcount == 0) {
            $this->AddRow($this->_itemempty);
        } else {
            $m = $this->_itemcount % $this->_coln;
            if ($m > 0) {
                while ($m < $this->_coln) {
                    $this->_row .= $this->_itemrest;
                    $m++;
                }
                $this->AddRow($this->_row);
                $this->_row = "";
            }
        }
        return $this->Tpl2HTML($this->_tabletpl, array("TableContent" => $this->_table), $this->_classname);
    }

}

?>