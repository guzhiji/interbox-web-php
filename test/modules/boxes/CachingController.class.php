<?php

class CachingController extends BoxModel {

    function __construct($args) {
        parent::__construct(__CLASS__);
        switch (isset($args['mode']) ? $args['mode'] : '') {
            case 'versioning':
            case 'timing':
                $this->tplName = $args['mode'];
                break;
            default:
                $this->tplName = 'nocache';
                break;
        }
    }

    protected function LoadContent() {
        return '';
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_cache'));
        $page->ShowHomeButton();
    }

    public function Before($page) {
        $this->SetField('elapsed', $page->GetElapsedMillis());
        $this->SetField('version', time() + 1);
    }

}