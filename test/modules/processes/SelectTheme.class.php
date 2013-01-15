<?php

class SelectTheme extends ProcessModel {

    public function Process() {
        $id = strGet('id');
        $list = GetThemes();
        if (isset($list[$id])) {
            SetThemeID($id);
            $this->Output('MsgBox', array(
                'msg' => GetLangData('theme_selected'),
                'url' => '?module=theme',
                'title' => GetLangData('page_theme')
            ));
        } else {
            $this->Output('MsgBox', array(
                'msg' => GetLangData('theme_notfound'),
                'url' => '?module=theme',
                'title' => GetLangData('page_theme')
            ));
        }
        return TRUE;
    }

}