<?php

class SelectLang extends ProcessModel {

    public function Process() {
        $lang = strGet('lang');
        $list = GetLanguages();
        if (isset($list[$lang])) {
            SetLang($lang);
            $this->Output('MsgBox', array(
                'msg' => GetLangData('lang_selected'),
                'url' => '?module=language',
                'title' => GetLangData('page_lang')
            ));
        } else {
            $this->Output('MsgBox', array(
                'msg' => GetLangData('lang_notfound'),
                'url' => '?module=language',
                'title' => GetLangData('page_lang')
            ));
        }
        return TRUE;
    }

}