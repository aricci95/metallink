<?php

class ProfileController extends AppController
{

    protected $_JS = array(JS_MODAL, JS_AUTOCOMPLETE);

    public function render()
    {
        if (empty($this->context->params['value'])) {
            $this->redirect('home', array('msg' => ERR_BLACKLISTED));
        }

        // Récupération des informations de l'utilisateur
        $user = $this->model->User->getUserByIdDetails($this->context->params['value']);

        if (empty($user)) {
            $this->redirect('home', array('msg' => ERR_BLACKLISTED));
        }

        $this->view->tasteTypes = $this->model->Taste->getTasteTypes();
        $tastes                 = $this->model->Taste->getTastes($this->context->params['value']);

        if (!empty($tastes)) {
            foreach ($tastes['data'] as $type => $tasteData) {
                if ($type == 'livres') {
                    foreach ($tasteData as $key => $value) {
                        $tastes['data'][$type][$key] = "<a target='_blank' href='http://www.allocine.fr/recherche/?q=".str_replace(' ', '+', trim($value))."' >".$value.'</a>';
                    }
                }
            }
        }

        $this->view->tastes = $tastes;

        // Ajout de la vue
        if ($this->context->get('user_id') != $this->context->params['value']) {
            $this->model->views->addView($this->context->params['value']);
        }

        $this->view->details = $this->model->User->convertBinaries($user);
        $this->view->addictions = $this->model->User->convertQuantities($user);

        $user['user_description'] = Tools::toSmiles($user['user_description']);
        $user['user_light_description'] = Tools::toSmiles($user['user_light_description']);

        if (empty($user['user_photo_url'])) {
            $user['user_photo_url'] = 'unknowUser.jpg';
        }

        $this->view->user = $user;

        // Récupération des photos
        $photos = $this->model->Photo->getPhotosByKey($this->context->params['value'], PHOTO_TYPE_USER);

        if (empty($photos)) {
            $photos = array(array('photo_url' => $user['user_photo_url']));
        }

        $this->view->photos = $photos;

        // Vérificiation état link
        $this->view->link = $this->model->Link->getLink($user['user_id']);
        $this->view->setViewName('user/wMain');
        $this->view->render();
    }

    public function renderEdit()
    {
        $this->view->addJS(JS_DATEPICKER);
        $this->view->addJS(JS_TASTE);

        // Récupération des informations de l'utilisateur
        $this->view->user = $this->model->User->getUserByIdDetails($this->context->get('user_id'));

        // Récupération des listes déroulantes
        $this->view->styles     = $this->model->find('style', array(), array(), array('style_libel'));
        $this->view->looks      = $this->model->find('ref_look', array(), array(), array('look_libel'));
        $this->view->quantities = $this->model->find('ref_quantity', array(), array(), array('quantity_libel'));

        // PASSIONS
        $types = $this->model->Taste->getTasteTypes();

        foreach ($types as $type) {
            if (!empty($this->context->getParam($type))) {
                $datas = $this->context->getParam($type);

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
        }

        $this->view->tastes = $this->model->Taste->getTastes();
        $this->view->tasteTypes = $types;

        $this->view->setTitle('Edition du profil');
        $this->view->setViewName('user/wEdit');
        $this->view->render();
    }

    public function renderSave()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->context->params['user_login'])) {
            $this->context->params['user_login']             = Tools::no_special_character($this->context->params['user_login']);
            $this->context->params['user_light_description'] = htmlspecialchars($this->context->params['user_light_description'], ENT_QUOTES, 'utf-8');
            $this->context->params['user_description']       = htmlspecialchars($this->context->params['user_description'], ENT_QUOTES, 'utf-8');
            $this->context->params['user_profession']        = htmlspecialchars($this->context->params['user_profession'], ENT_QUOTES, 'utf-8');

            // On vérifie si le mdp est ok
            if ($this->context->params['user_pwd'] != $this->context->params['verif_pwd']) {
                $this->view->growler("les deux mots de passes ne sont pas identiques.", GROWLER_ERR);
            } else {
                if (!empty($this->context->params['user_pwd'])) {
                    $this->context->params['user_pwd'] = md5($this->context->params['user_pwd']);
                }

                // On formate la date de naissance
                if (!empty($this->context->params['user_birth'])) {
                    $dt = DateTime::createFromFormat('d/m/Y', $this->context->params['user_birth']);
                    $this->context->params['user_birth'] = $dt->format("Y-m-d");
                }

                if ($this->model->User->updateUserById($this->context->params)) {
                    $ville = $this->model->city->findOne(array('ville_longitude_deg', 'ville_latitude_deg'), array('ville_id' => $this->context->getParam('ville_id')));

                    $this->context->set('ville_longitude_deg', $ville['ville_longitude_deg']);
                    $this->context->set('ville_latitude_deg', $ville['ville_latitude_deg']);

                    $this->view->growler('Modifications enregistrées', GROWLER_OK);
                } else {
                    $this->view->growlerError();
                }
            }
        }

        $this->renderEdit();
    }

    public function renderBlock()
    {
        $this->_block(true);
    }

    public function renderUnblock()
    {
        $this->_block(false);
    }

    private function _block($block = true)
    {
        if (!empty($this->context->params['value'])) {
            if ($block) {
                $status = $this->get('link')->getLinkStatus($this->context->params['value']);
                if ($status == LINK_STATUS_NONE) {
                    if ($this->model->Link->block($this->context->params['value'])) {
                        $this->view->growler('Utilisateur bloqué.', GROWLER_OK);
                    } else {
                        $this->view->growlerError();
                    }
                } else {
                    if ($this->model->Link->updateLink($this->context->params['value'], LINK_STATUS_BLACKLIST)) {
                        $this->view->growler('Utilisateur bloqué.', GROWLER_OK);
                    } else {
                        $this->view->growlerError();
                    }
                }
            } else {
                if ($this->model->Link->unlink($this->context->params['value'])) {
                        $this->view->growler('Utilisateur débloqué.', GROWLER_OK);
                } else {
                    $this->view->growlerError();
                }
            }
        }
        $this->render();
    }

    public function renderDelete()
    {
        // Suppression de l'utilisateur
        if ($this->get('user')->delete($this->context->get('user_id'))) {

            //Destruction du Cookie
            setcookie("MlinkPwd", 0);
            setcookie("MlinkLogin", 0);
            session_destroy();

            // redirection
            $this->redirect('home', array('msg' => MSG_ACCOUNT_DELETED));
        } else {
            $this->_view->growlerError();
            $this->render();
        }
    }
}
