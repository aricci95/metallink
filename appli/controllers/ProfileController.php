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
        $this->_view->tasteTypes = $this->_model->Taste->getTasteTypes();
        $tastes                  = $this->_model->Taste->getTastes($this->params['value']);
        if (!empty($tastes)) {
            foreach ($tastes['data'] as $type => $tasteData) {
                if ($type == "groupes") {
                    foreach ($tasteData as $bandKey => $band) {
                        $tastes['data'][$type][$bandKey] = "<a target='_blank' href='http://www.spirit-of-metal.com/find.php?search=all&l=fr&nom=".str_replace(' ', '+', trim($band))."' >".$band.'</a>';
                    }
                }
            }
        }
        $this->_view->tastes = $tastes;

        // Récupération des informations de l'utilisateur
        $user = $this->_model->User->getById($this->params['value']);
        // Ajout de la vue
        if ($this->getContextUser('id') != $this->params['value']) {
            $this->_model->views->addView($this->params['value']);
        }
        $this->_view->details = $this->_model->User->convertBinaries($user);
        $this->_view->addictions = $this->_model->User->convertQuantities($user);

        $user['user_description'] = Tools::toSmiles($user['user_description']);
        $user['user_light_description'] = Tools::toSmiles($user['user_light_description']);
        if (empty($user['user_photo_url'])) {
            $user['user_photo_url'] = 'unknowUser.jpg';
        }
        $this->_view->user = $user;
        // Récupération des photos
        $photos = $this->_model->Photo->getPhotosByKey($this->params['value'], PHOTO_TYPE_USER);
        if (empty($photos)) {
            $photos = array(array('photo_url' => $user['user_photo_url']));
        }
        $this->_view->photos = $photos;
        // Vérificiation état link
        $this->_view->link = $this->_model->Link->getLink($user['user_id']);
        // Info de localisation
        $this->_view->geoloc = array();
        $contextUserCity = $this->getContextUser('city');
        if (!empty($user['user_city']) && !empty($contextUserCity)) {
            $this->_view->geoloc = $this->_getDistance($contextUserCity, $user['user_city']);
        }
        $this->_view->setViewName('user/wMain');

        $this->_view->render();
    }

    public function renderEdit()
    {
        $this->_view->addJS(JS_DATEPICKER);
        // Récupération des informations de l'utilisateur
        $this->_view->user = $this->_model->User->getUserByIdDetails($this->getContextUser('id'));

        // Récupération des listes déroulantes
        $this->_view->hairs      = $this->_model->getItemsFromTable('ref_hair');
        $this->_view->styles     = $this->_model->getItemsFromTable('ref_style');
        $this->_view->eyes       = $this->_model->getItemsFromTable('ref_eyes');
        $this->_view->looks      = $this->_model->getItemsFromTable('ref_look');
        $this->_view->origins    = $this->_model->getItemsFromTable('ref_origin');
        $this->_view->quantities = $this->_model->getItemsFromTable('ref_quantity');

        $this->_view->setTitle('Edition du profil');
        $this->_view->setViewName('user/wEdit');
        $this->_view->render();
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
                $this->_view->growler("les deux mots de passes ne sont pas identiques.", GROWLER_ERR);
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
                if ($this->_model->User->updateUserById($this->params)) {
                    $this->_view->growler('Modifications enregistrées', GROWLER_OK);
                } else {
                    $this->_view->growlerError();
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
                $status = $this->getLinkStatus($this->params['value']);
                if ($status == LINK_STATUS_NONE) {
                    if ($this->_model->Link->block($this->params['value'])) {
                        $this->_view->growler('Utilisateur bloqué.', GROWLER_OK);
                    } else {
                        $this->_view->growlerError();
                    }
                } else {
                    if ($this->_model->Link->updateLink($this->params['value'], LINK_STATUS_BLACKLIST)) {
                        $this->_view->growler('Utilisateur bloqué.', GROWLER_OK);
                    } else {
                        $this->_view->growlerError();
                    }
                }
            } else {
                if ($this->_model->Link->unlink($this->params['value'])) {
                        $this->_view->growler('Utilisateur débloqué.', GROWLER_OK);
                } else {
                    $this->_view->growlerError();
                }
            }
        }
        $this->render();
    }

    public function renderDelete()
    {
        // Suppression de l'utilisateur
        $this->_model->User->deleteUserById($this->getContextUser('id'));
        //Destruction du Cookie
        setcookie("MlinkPwd", 0);
        setcookie("MlinkLogin", 0);
        session_destroy();
        // redirection
        $this->redirect('home', array('msg' => MSG_ACCOUNT_DELETED));
    }
}
