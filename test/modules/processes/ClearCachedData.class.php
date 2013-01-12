<?php

class ClearCachedData extends ProcessModel {

    public function Process() {
        ClearCache();
        $this->Output('MsgBox', array(
            'msg' => GetLangData('cache_clear_finish'),
            'url' => '?module=cache'
        ));
        return TRUE;
    }

}