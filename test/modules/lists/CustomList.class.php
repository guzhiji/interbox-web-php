<?php

LoadIBC1Class('ListModel', 'framework');

class CustomList extends ListModel {

    function __construct($mode) {
        parent::__construct($mode . '_item', __CLASS__);
        $this->tplName = $mode . '_list';
    }

    public function SetContainer(array $vars = array()) {
        parent::SetContainer($this->tplName, $vars);
    }

    public function After($page) {
        
    }

    public function Before($page) {
        
    }

}