<?php

class DeleteConfig extends ProcessModel {

    public function Process() {
        $key = strGet('key');
        if (isset($_GET['confirmed'])) {
            try {
                LoadIBC1Class('ConfigDataGroupEditor', 'framework');
                $editor = new ConfigDataGroupEditor();
                $editor->RemoveValue($key);
                $editor->Persist();
                $this->Output('MsgBox', array(
                    'msg' => GetLangData('config_delete_finish'),
                    'url' => '?module=configuration',
                    'title' => GetLangData('page_config')
                ));
            } catch (Exception $ex) {
                $this->Output('MsgBox', array(
                    'msg' => GetLangData('config_delete_fail'),
                    'url' => queryString(array(
                        'module' => 'configuration/editor',
                        'key' => $key
                    )),
                    'title' => GetLangData('page_config')
                ));
            }
        } else {
            $this->Output('MsgBox', array(
                'mode' => 'confirm',
                'title' => GetLangData('page_config'),
                'msg' => GetLangData('config_delete_confirm'),
                'url' => queryString(array(
                    'module' => 'configuration/editor',
                    'function' => 'delete',
                    'key' => $key,
                    'confirmed' => 'yes'
                ))
            ));
        }
        return TRUE;
    }

}