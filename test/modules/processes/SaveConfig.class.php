<?php

class SaveConfig extends ProcessModel {

    public function Process() {
        $key = strPost('conf_key');
        $value = strPost('conf_value');
        try {
            LoadIBC1Class('ConfigDataGroupEditor', 'framework');
            $editor = new ConfigDataGroupEditor();
            $editor->SetValue($key, $value);
            $editor->Persist();
            $this->Output('MsgBox', array(
                'msg' => GetLangData('config_save_finish'),
                'url' => '?module=configuration'
            ));
        } catch (Exception $ex) {
            $this->Output('MsgBox', array(
                'msg' => GetLangData('config_save_fail'),
                'url' => '?module=configuration'
            ));
        }
        return TRUE;
    }

}