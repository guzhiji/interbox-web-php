<?php

LoadIBC1Class('ListModel', 'framework');

class CustomList extends ListModel {

    private $mode;

    function __construct($mode) {
        parent::__construct($mode . '_item', __CLASS__);
        $this->mode = $mode;
        parent::SetContainer($mode . '_list', array());
    }

    public function SetContainer(array $vars = array()) {
        parent::SetContainer($this->mode . '_list', $vars);
    }

}