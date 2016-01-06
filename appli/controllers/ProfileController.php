<?php

class ProfileController extends AppController
{

    protected $_JS = array(JS_MODAL, JS_AUTOCOMPLETE);

    private function _getDistance($adresse1, $adresse2)
    {
        $distance = 0;
        $duree    = 0;
        $ville    = null;
        if (!empty($adresse1) && !is_numeric($adresse1) && !empty($adresse2) && !is_numeric($adresse2)) {
            $url = 'http://maps.google.com/maps/api/directions/xml?language=fr&origin='.$adresse1.'&destination='.$adresse2.'&sensor=false';
            $url = str_replace(' ', '%20', $url);
            $xml = @file_get_contents($url);
            if (!empty($xml)) {
                $root = simplexml_load_string($xml);
                if (!empty($root->route->leg)) {
                    $distance = round($root->route->leg->distance->value / 1000);
                    $duree    = $root->route->leg->duration->value;
                    $etapes   = $root->route->leg->step;
                    $ville    = $root->route->leg->end_address;
                }
            }
        }
        return array('distance' => $distance, 'duree' => $duree, 'ville' => $ville);
    }

    public function render()
    {
        $this->view->tasteTypes = $this->model->Taste->getTasteTypes();
        $tastes                 = $this->model->Taste->getTastes($this->params['value']);

        if (!empty($tastes)) {
            foreach ($tastes['data'] as $type => $tasteData) {
                if ($type == "groupes") {
                    foreach ($tasteData as $bandKey => $band) {
                        $tastes['data'][$type][$bandKey] = "<a target='_blank' href='http://www.spirit-of-metal.com/find.php?search=all&l=fr&nom=".str_replace(' ', '+', trim($band))."' >".$band.'</a>';
                    }
                }
            }
        }

        $this->view->tastes = $tastes;

        // Récupération des informations de l'utilisateur
        $user = $this->model->User->getUserByIdDetails($this->params['value']);

        // Ajout de la vue
        if (User::getContextUser('id') != $this->params['value']) {
            $this->model->views->addView($this->params['value']);
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
        $photos = $this->model->Photo->getPhotosByKey($this->params['value'], PHOTO_TYPE_USER);

        if (empty($photos)) {
            $photos = array(array('photo_url' => $user['user_photo_url']));
        }

        $this->view->photos = $photos;

        // Vérificiation état link
        $this->view->link = $this->model->Link->getLink($user['user_id']);

        // Info de localisation
        $this->view->geoloc = array();
        $contextUserCity = User::getContextUser('city');

        if (!empty($user['user_city']) && !empty($contextUserCity)) {
            $this->view->geoloc = $this->_getDistance($contextUserCity, $user['user_city']);
        }

        $this->view->setViewName('user/wMain');

        $this->view->render();
    }

    public function renderEdit()
    {
        $this->view->addJS(JS_DATEPICKER);

        // Récupération des informations de l'utilisateur
        $this->view->user = $this->model->User->getUserByIdDetails(User::getContextUser('id'));

        // Récupération des listes déroulantes
        $this->view->styles     = $this->model->getItemsFromTable('ref_style');
        $this->view->looks      = $this->model->getItemsFromTable('ref_look');
        $this->view->quantities = $this->model->getItemsFromTable('ref_quantity');

        $this->view->setTitle('Edition du profil');
        $this->view->setViewName('user/wEdit');
        $this->view->render();
    }

    public function renderSave()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($this->params['user_login'])) {
            $this->params['user_login']             = Tools::no_special_character($this->params['user_login']);
            $this->params['user_light_description'] = htmlentities($this->params['user_light_description'], ENT_QUOTES, 'utf-8');
            $this->params['user_description']       = htmlentities($this->params['user_description'], ENT_QUOTES, 'utf-8');
            $this->params['user_profession']        = htmlentities($this->params['user_profession'], ENT_QUOTES, 'utf-8');

            // On vérifie si le mdp est ok
            if ($this->params['user_pwd'] != $this->params['verif_pwd']) {
                $this->view->growler("les deux mots de passes ne sont pas identiques.", GROWLER_ERR);
            } else {
                if (!empty($this->params['user_pwd'])) {
                    $this->params['user_pwd'] = md5($this->params['user_pwd']);
                }
                // On formate la date de naissance
                if (!empty($this->params['user_birth'])) {
                    $dt = DateTime::createFromFormat('d/m/Y', $this->params['user_birth']);
                    $this->params['user_birth'] = $dt->format("Y-m-d");
                }
                $this->params['user_city'] = str_replace("'", " ", $this->params['user_city']);
                if ($this->model->User->updateUserById($this->params)) {
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
        if (!empty($this->params['value'])) {
            if ($block) {
                $status = Link::getStatus($this->params['value']);
                if ($status == LINK_STATUS_NONE) {
                    if ($this->model->Link->block($this->params['value'])) {
                        $this->view->growler('Utilisateur bloqué.', GROWLER_OK);
                    } else {
                        $this->view->growlerError();
                    }
                } else {
                    if ($this->model->Link->updateLink($this->params['value'], LINK_STATUS_BLACKLIST)) {
                        $this->view->growler('Utilisateur bloqué.', GROWLER_OK);
                    } else {
                        $this->view->growlerError();
                    }
                }
            } else {
                if ($this->model->Link->unlink($this->params['value'])) {
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
        if ($this->get('user')->delete(User::getContextUser('id'))) {

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
