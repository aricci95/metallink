<?php

class TasteController extends AppController
{

    protected $_JS = array(JS_TASTE);

    public function render()
    {
        $types = $this->model->Taste->getTasteTypes();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($types as $type) {
                if (!empty($this->params[$type])) {
                    $datas = $this->params[$type];
                    foreach ($datas as $key => $data) {
                        if ($data == '') {
                            unset($datas[$key]);
                        } else {
                            $datas[$key] = htmlspecialchars(trim($data), ENT_QUOTES, 'utf-8');
                        }
                    }
                    $tastes[$type] = $datas;
                }
            }
            if (!empty($tastes)) {
                $this->model->Taste->save($tastes);
                $this->view->growler('Modifications enregistrées', GROWLER_OK);
            }
        }
        $this->view->tastes = $this->model->Taste->getTastes();
        $this->view->tasteTypes = $types;
        $this->view->setTitle('Edition des goûts');
        $this->view->setViewName('taste/wList');
        $this->view->render();
    }
}
