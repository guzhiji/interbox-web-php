<?php

class SelectTheme extends ProcessModel {

    public function Process() {
        $id = strGet('id');
        $list = GetThemes();
        if (isset($list[$id])) {
            SetThemeID($id);
            $this->Output('MsgBox', array(
                'msg' => GetLangData('theme_selected'),
                'url' => '?module=theme'
            ));
        } else {
            $this->Output('MsgBox', array(
                'msg' => GetLangData('theme_notfound'),
                'url' => '?module=theme'
            ));
        }
        return TRUE;
    }

}