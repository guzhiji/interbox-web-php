<?php

class ConfigEditor extends BoxModel {

    private $state;

    function __construct($args) {
        parent::__construct('Content', '', __CLASS__);
        //region=Content
        //tpl=
    }

    protected function LoadContent() {
        $key = strGet('key');
        if (!empty($key)) {
            $value = GetConfigValue($key);
            if ($value !== NULL) {
                //found
                $this->state = 'update';
                return TransformTpl('update', array(
                            'module' => $this->module,
                            'urlparam_key' => $key,
                            'text_key' => $key,
                            'text_value' => $value
                                ), __CLASS__);
            }
        }
        $this->state = 'add';
        return TransformTpl('add', array(
                    'module' => $this->module
                        ), __CLASS__);
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_config'));
        if ($this->state == 'update')
            $page->ShowHomeButton('?module=configuration');
    }

    public function Before($page) {
        
    }

}