<?php

class SelectTheme extends ProcessModel {

    public function Process() {
        $id = strGet('id');
        $list = GetThemes();
        if (isset($list[$id])) {
            if (isset($_GET['confirmed'])) {
                SetThemeID($id);
                $this->Output('MsgBox', array(
                    'msg' => $list[$id],
                    'url' => '?module=theme'
                ));
            } else {
                $this->Output('MsgBox', array(
                    'mode' => 'confirm',
                    'msg' => $list[$id] . '?',
                    'url' => queryString(array(
                        'module' => 'theme',
                        'function' => 'select',
                        'id' => $id,
                        'confirmed' => 'yes'
                    ))
                ));
            }
        } else {
            $this->Output('MsgBox', array(
                'msg' => $id . ' not found',
                'url' => '?module=theme'
            ));
        }
        return TRUE;
    }

}