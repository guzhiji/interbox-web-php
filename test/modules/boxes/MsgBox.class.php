<?php

class MsgBox extends BoxModel {

    private $title;

    function __construct($args) {
        parent::__construct(__CLASS__);
        $this->tplName = (isset($args['mode']) && $args['mode'] == 'confirm') ? 'confirm' : 'msg';
        $this->title = $args['title'];
        $this->SetField('Message', htmlspecialchars($args['msg']));
        $this->SetField('URL', $args['url']);
        $this->SetField('HTML', isset($args['content']) ? $args['content'] : '');
    }

    protected function LoadContent() {
        return '';
    }

    public function After($page) {
        $page->SetTitle($this->title);
    }

    public function Before($page) {
        
    }

}
