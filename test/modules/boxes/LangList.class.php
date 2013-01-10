<?php

LoadIBC1Class('ListModel', 'framework');

class LangList extends BoxModel {

    function __construct($args) {
        parent::__construct('Content', 'form', __CLASS__);
        //region=Content
        //tpl=form
    }

    protected function LoadContent() {
        $this->SetField('module', $this->module);
        $selected = GetLang();
        $lang = GetLanguages();
        require_once GetSysResPath('CustomList.class.php', 'modules/lists');
        $list = new CustomList('select1');
        $list->SetContainer(array(
            'text_label' => '',
            'text_ele_id' => 'lang',
            'text_ele_name' => 'lang'
        ));
        foreach ($lang as $code => $name) {
            $list->AddItem(array(
                'id' => htmlspecialchars($code),
                'text_name' => $name,
                'selected' => (($selected == $code) ? ' selected="selected"' : '')
            ));
        }
        return $list->GetHTML();
    }

    public function After($page) {
        $page->SetTitle(GetLangData('page_lang'));
        $page->ShowHomeButton();
    }

    public function Before($page) {
        
    }

}