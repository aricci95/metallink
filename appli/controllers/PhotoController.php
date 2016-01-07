<?php

class PhotoController extends AppController
{

    protected $_JS = array(JS_PHOTO);

    private $_photoId;
    private $_typeId;
    private $_keyId;
    private $_objectName;
    private $_objectPhotoUrl;

    public function __construct()
    {
        parent::__construct();

        if (isset($this->params['value'])) {
            $this->_typeId = $this->params['value'];
        }

        if (isset($this->params['type_id'])) {
            $this->_typeId = $this->params['type_id'];
        }

        if (!empty($this->params['key_id'])) {
            $this->_keyId = $this->params['key_id'];
        } elseif ($this->_typeId == PHOTO_TYPE_USER) {
            $this->_keyId = User::getContextUser('id');
        } elseif (!empty($this->params['option'])) {
            $this->_keyId = $this->params['option'];
        }

        $this->_objectName     = ($this->_typeId == PHOTO_TYPE_USER) ? 'user' : 'article';
        $this->_objectPhotoUrl = ($this->_typeId == PHOTO_TYPE_USER) ? 'user_photo_url' : 'art_photo_url';
    }

    public function render()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['new_photo'])) {
            try {
                $this->get('photo')->uploadImage($_FILES['new_photo']['name'], $_FILES['new_photo']['tmp_name'], $this->_typeId, $this->_keyId);
                $this->view->growler('Photo ajoutée', GROWLER_OK);
            } catch (Exception $e) {
                $this->view->growler($e->getMessage(), GROWLER_ERR);
            }
        }

        $photos = $this->model->photo->getPhotosByKey($this->_keyId, $this->_typeId);

        if (count($photos) == 1) {
            $this->_setProfilePhoto($photos[0]);
        }

        $this->view->setTitle('Edition des photos');
        $this->view->setViewName('photo/wEdit');

        $this->view->typeId = $this->_typeId;
        $this->view->keyId  = $this->_keyId;
        $this->view->photos = $photos;

        if ($this->_typeId == PHOTO_TYPE_USER) {
            $this->view->mainPhotoUrl = User::getContextUser('photo_url');
        } else {
            $object = $this->model->{$this->_objectName}->getById($this->_keyId);
            if (!empty($object)) {
                $this->view->mainPhotoUrl = $object[$this->_objectPhotoUrl];
            }
        }

        $this->view->render();
    }

    public function renderSetProfilePhoto()
    {
        $photo['type_id']   = $this->_typeId;
        $photo['key_id']    = $this->_keyId;
        $photo['photo_url'] = $this->params['photo_url'];

        $this->_setProfilePhoto($photo);
        $this->view->photos = $this->model->Photo->getPhotosByKey($photo['key_id'], $photo['type_id']);

        if ($this->_typeId == PHOTO_TYPE_USER) {
            $this->view->mainPhotoUrl = User::getContextUser('photo_url');
        } else {
            $object = $this->model->{$this->_objectName}->getById($this->_keyId);
            if (!empty($object)) {
                $this->view->mainPhotoUrl = $object[$this->_objectPhotoUrl];
            }
        }

        $this->view->getJSONResponse('photo/wItems');

        return JSON_OK;
    }

    private function _setProfilePhoto($photo)
    {
        if ($this->_typeId == PHOTO_TYPE_USER) {
            $_SESSION['user_photo_url'] = $photo['photo_url'];
        }

        $this->model->photo->setProfilePhoto($photo);

        return JSON_OK;
    }

    public function renderRemovePhoto()
    {
        $id   = $this->params['photo_id'];
        $path = $this->params['photo_url'];

        if ($this->get('photo')->delete($id, $path)) {
            return JSON_OK;
        } else {
            return JSON_ERR;
        }
    }
}
