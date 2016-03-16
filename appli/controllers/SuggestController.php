<?php

class SuggestController extends AppController
{

    public function renderCity()
    {
        $this->_suggest('city');
    }

    public function renderConcert()
    {
        $this->_suggest('concert');
    }

    private function _suggest($type)
    {
        $data   = array();

        $string = $this->context->params['value'];

        if (!empty($string)) {
            $data = $this->model->$type->suggest($string);
        }

        echo json_encode($data);
    }
}
