<?php

class SelectLang extends ProcessModel {

    public function Process() {
        $lang = strGet('lang');
        $list = GetLanguages();
        if (isset($list[$lang])) {
            if (isset($_GET['confirmed'])) {
                SetLang($lang);
                $this->Output('MsgBox', array(
                    'msg' => $list[$lang],
                    'url' => '?module=language'
                ));
            } else {
                $this->Output('MsgBox', array(
                    'mode' => 'confirm',
                    'msg' => $list[$lang] . '?',
                    'url' => queryString(array(
                        'module' => 'language',
                        'function' => 'select',
                        'lang' => $lang,
                        'confirmed' => 'yes'
                    ))
                ));
            }
        } else {
            $this->Output('MsgBox', array(
                'msg' => $lang . ' not found',
                'url' => '?module=language'
            ));
        }
        return TRUE;
    }

}