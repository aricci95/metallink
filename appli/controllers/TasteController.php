<?php

class TasteController extends AppController
{

    protected $_JS = array(JS_TASTE);

    public function render()
    {
        $types = $this->_model->Taste->getTasteTypes();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($types as $type) {
                if (!empty($this->params[$type])) {
                    $datas = $this->params[$type];
                    foreach ($datas as $key => $data) {
                        if ($data == '') {
                            unset($datas[$key]);
                        } else {
                            $datas[$key] = htmlentities(trim($data), ENT_QUOTES, 'utf-8');
                        }
                    }
                    $tastes[$type] = $datas;
                }
            }
            if (!empty($tastes)) {
                $this->_model->Taste->save($tastes);
                $this->_view->growler('Modifications enregistrées', GROWLER_OK);
            }
        }
        $this->_view->tastes = $this->_model->Taste->getTastes();
        $this->_view->tasteTypes = $types;
        $this->_view->setTitle('Edition des goûts');
        $this->_view->setViewName('taste/wList');
        $this->_view->render();
    }
}
