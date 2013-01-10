<?php

class Welcome extends BoxModel {

    function __construct($args) {
        parent::__construct('Content', 'welcome', __CLASS__);
        //region=Content
        //tpl=welcome
    }

    protected function LoadContent() {
        return '';
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_home'));
    }

    public function Before($page) {
        
    }

}
