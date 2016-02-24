<?php

class SuggestController extends AppController
{

    public function renderVille()
    {
        $this->_suggest('ville');
    }

    public function renderConcert()
    {
        $this->_suggest('concert');
    }

    private function _suggest($type)
    {
        $string = $this->context->params['value'];
        $datas  = array();
        if (!empty($string)) {
            $datas = $this->model->load($type)->suggest($string);
        }
        echo json_encode($datas);
    }

    public function renderAdd()
    {
        echo $this->model->load($this->context->params['type'])->add($this->context->params['string']);
    }
}
