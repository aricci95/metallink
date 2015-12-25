<?php

class SubscribeController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function render()
    {
        $this->view->setViewName('wSubscribe');
        $this->view->setTitle('Inscription');
        $this->view->render();
    }

    private function _isValid()
    {
        // Agreements
        if (empty($this->params['agreements']) || $this->params['agreements'] != 'on') {
            $this->view->growler("Vous devez accepter les mentions légales de MetalLinK.");
            return false;
        }
        // Champs vides
        $inputs = array('user_login', 'user_pwd', 'verif_pwd', 'user_mail', 'agreements');
        foreach ($inputs as $input) {
            if (empty($this->params[$input])) {
                $this->view->growler('Tous les champs sont obligatoires.');
                return false;
            }
        }
        // Pseudo
        $this->params['user_login'] = trim($this->params['user_login']);
        if (strlen($this->params['user_login']) > 20 || $this->model->hasSpecialChar($this->params['user_login'])) {
            $this->view->growler('Le pseudo doit faire moins de 20 caractères et ne comporter ni espace ni caractères spéciaux.');
            return false;
        }
        if ($this->model->User->isUsedLogin($this->params['user_login'])) {
            $this->view->growler('Pseudo déjà utilisé.');
            return false;
        }
        // Mail
        $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
        if (!preg_match($Syntaxe, $this->params['user_mail'])) {
            $this->view->growler('Adresse e-mail invalide.');
            return false;
        }
        if ($this->model->User->isUsedMail($this->params['user_mail'])) {
            $this->view->growler('Adresse e-mail déjà utilisée.');
            return false;
        }
        // Password
        if (strlen($this->params['user_pwd']) < 8) {
            $this->view->growler('Le mot de passe doit comporter au moins 8 caractères.');
            return false;
        }
        if ($this->params['user_pwd'] != $this->params['verif_pwd']) {
            $this->view->growler('La vérification du mot de passe est érronnée.');
            return false;
        }

        return true;
    }

    public function renderSave()
    {
        $contextUserId = User::getContextUser('id');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($contextUserId)) {
            if ($this->_isValid()) {
                $newUser['user_login']  = $this->params['user_login'];
                $newUser['user_pwd']    = md5($this->params['user_pwd']);
                $newUser['user_mail']   = $this->params['user_mail'];
                $newUser['user_gender'] = $this->params['user_gender'];
                $validationId = $this->model->User->createUser($newUser);

                if (!empty($validationId)) {
                    $message = 'Merci de vous être inscris sur MetalLink<br><br>
                            Avant de pouvoir vous connecter vous devez cliquer sur ce lien pour valider votre adresse mail :<br><br>
                            <a href="http://metallink.fr/MLink/subscribe/validate/'.$validationId.'">Cliquez ici pour valider votre compte ! </a><br><br>
                            Voici vos identifiants :<br><br>
                            <u>Login :</u> '.$newUser['user_login'].'<br><br>
                            <u>Mot de passe :</u> '.$this->params['user_pwd'].'<br><br>
                            Si vous rencontrez des problèmes, n\'hésitez pas à nous envoyer un mail en répondant directement à celui-ci, nous vous répondrons dans les plus bref délais.';
                    if (Mailer::send($newUser['user_mail'], 'Bienvenue sur MetalLink '.$newUser['user_login'].' !', $message)) {
                        $this->redirect('home', array('msg' => MSG_VALIDATION_SENT));
                    } else {
                        $this->view->growlerError();
                    }
                    return;
                } else {
                    $this->view->growlerError();
                }
            }
        }

        $this->render();
    }

    public function renderValidate()
    {
        if (!empty($this->params['value'])) {
            if ($this->model->User->setValid($this->params['value'])) {
                $this->redirect('home', array('msg' => MSG_VALIDATION_SUCCESS));
            } else {
                $this->redirect('home', array('msg' => ERR_VALIDATION_FAILURE));
            }
        } else {
            $this->redirect('home', array('msg' => ERR_VALIDATION_FAILURE));
        }
    }

    public function renderTerms()
    {
        $this->view->setTitle('Mentions Légales');
        $this->view->setViewName('wTerms');
        $this->view->render();
    }
}
