<?php

class CachingController extends BoxModel {

    function __construct($args) {
        parent::__construct('Content', $this->getTpl($args), __CLASS__);
    }

    private function getTpl($args) {
        switch (isset($args['mode']) ? $args['mode'] : '') {
            case 'versioning':
            case 'timing':
                return $args['mode'];
            default:
                return 'nocache';
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