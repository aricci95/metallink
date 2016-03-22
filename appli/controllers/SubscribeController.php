<?php

class SubscribeController extends AppController
{

    protected $_authLevel = AUTH_LEVEL_NONE;

    public function __construct()
    {
        parent::__construct();

        if ($this->context->get('user_id')) {
            $this->redirect('user');
        }
    }

    public function render()
    {
        $socialAppsData = $this->context->get('userprofile');

        if (empty($this->context->params) && !empty($socialAppsData['email'])) {
            $this->context->params['user_mail']   = $socialAppsData['email'];
            $this->context->params['user_login']  = ucfirst(trim($socialAppsData['last_name']));
            $this->context->params['user_gender'] = ($socialAppsData['gender'] == 'male') ? '1' : '2';
        }

        $this->view->setViewName('wSubscribe');
        $this->view->setTitle('Inscription');
        $this->view->render();
    }

    private function _isValid()
    {
        // Agreements
        if (empty($this->context->params['agreements']) || $this->context->params['agreements'] != 'on') {
            $this->view->growler("Vous devez accepter les mentions légales de MetalLinK.");
            return false;
        }
        // Champs vides
        $inputs = array('user_login', 'user_pwd', 'verif_pwd', 'user_mail', 'agreements');
        foreach ($inputs as $input) {
            if (empty($this->context->params[$input])) {
                $this->view->growler('Tous les champs sont obligatoires.');
                return false;
            }
        }
        // Pseudo
        $this->context->params['user_login'] = trim($this->context->params['user_login']);
        if (strlen($this->context->params['user_login']) > 20 || $this->model->hasSpecialChar($this->context->params['user_login'])) {
            $this->view->growler('Le pseudo doit faire moins de 20 caractères et ne comporter ni espace ni caractères spéciaux.');
            return false;
        }
        if ($this->model->User->isUsedLogin($this->context->params['user_login'])) {
            $this->view->growler('Pseudo déjà utilisé.');
            return false;
        }
        // Message
        $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
        if (!preg_match($Syntaxe, $this->context->params['user_mail'])) {
            $this->view->growler('Adresse e-message invalide.');
            return false;
        }
        if ($this->model->User->isUsedEmail($this->context->params['user_mail'])) {
            $this->view->growler('Adresse e-message déjà utilisée.');
            return false;
        }
        // Password
        if (strlen($this->context->params['user_pwd']) < 8) {
            $this->view->growler('Le mot de passe doit comporter au moins 8 caractères.');
            return false;
        }
        if ($this->context->params['user_pwd'] != $this->context->params['verif_pwd']) {
            $this->view->growler('La vérification du mot de passe est érronnée.');
            return false;
        }

        return true;
    }

    public function renderSave()
    {
        $this->context->delete('userprofile');

        $contextUserId = $this->context->get('user_id');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($contextUserId)) {
            if ($this->_isValid()) {
                $newUser['user_login']  = $this->context->params['user_login'];
                $newUser['user_pwd']    = md5($this->context->params['user_pwd']);
                $newUser['user_mail']   = $this->context->params['user_mail'];
                $newUser['user_gender'] = $this->context->params['user_gender'];
                $validationId = $this->model->User->createUser($newUser);

                if (!empty($validationId)) {
                    $message = 'Merci de vous être inscris sur MetalLink<br><br>
                            Avant de pouvoir vous connecter vous devez cliquer sur ce lien pour valider votre adresse message :<br><br>
                            <a href="http://metallink.fr/MLink/subscribe/validate/'.$validationId.'">Cliquez ici pour valider votre compte ! </a><br><br>
                            Voici vos identifiants :<br><br>
                            <u>Login :</u> '.$newUser['user_login'].'<br><br>
                            <u>Mot de passe :</u> '.$this->context->params['user_pwd'].'<br><br>
                            Si vous rencontrez des problèmes, n\'hésitez pas à nous envoyer un message en répondant directement à celui-ci, nous vous répondrons dans les plus bref délais.';
                    if ($this->get('mailer')->send($newUser['user_mail'], 'Bienvenue sur MetalLink '.$newUser['user_login'].' !', $message)) {
                        $this->redirect('user', array('msg' => MSG_VALIDATION_SENT));
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
        if (!empty($this->context->params['value'])) {
            if ($this->model->User->setValid($this->context->params['value'])) {
                $this->redirect('user', array('msg' => MSG_VALIDATION_SUCCESS));
            } else {
                $this->redirect('user', array('msg' => ERR_VALIDATION_FAILURE));
            }
        } else {
            $this->redirect('user', array('msg' => ERR_VALIDATION_FAILURE));
        }
    }

    public function renderTerms()
    {
        $this->view->setTitle('Mentions Légales');
        $this->view->setViewName('wTerms');
        $this->view->render();
    }
}
