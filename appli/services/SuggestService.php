<?php

class SuggestService extends Service
{
	public function suggest($type, $index)
    {
        $data = $this->model->find(
        	$type,
        	array($index),
        	array('%' . $index, trim($this->context->getParam('value'))),
        	array('0, 10')
        );

        echo json_encode($data);
    }

    public function renderAdd()
    {
        echo $this->model->load($this->context->params['type'])->add($this->context->params['string']);
    }
}