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
            $photo['type_id'] = $this->_typeId;
            $photo['key_id']  = $this->_keyId;
            $photo = $this->_model->Photo->uploadImage(array_merge($_FILES['new_photo'], $photo), $this->_view);
            if (!empty($photo)) {
                $this->_view->growler('Photo ajoutée', GROWLER_OK);
            }
        }
        $photos = $this->_model->photo->getPhotosByKey($this->_keyId, $this->_typeId);
        if (count($photos) == 1) {
            $this->_setProfilePhoto($photos[0]);
        }
        $this->_view->setTitle('Edition des photos');
        $this->_view->setViewName('photo/wEdit');

        $this->_view->typeId = $this->_typeId;
        $this->_view->keyId  = $this->_keyId;
        $this->_view->photos = $photos;
        if ($this->_typeId == PHOTO_TYPE_USER) {
            $this->_view->mainPhotoUrl = User::getContextUser('photo_url');
        } else {
            $object = $this->_model->{$this->_objectName}->getById($this->_keyId);
            if (!empty($object)) {
                $this->_view->mainPhotoUrl = $object[$this->_objectPhotoUrl];
            }
        }

        $this->_view->render();
    }

    public function renderSetProfilePhoto()
    {
        $photo['type_id']   = $this->_typeId;
        $photo['key_id']    = $this->_keyId;
        $photo['photo_url'] = $this->params['photo_url'];

        $this->_setProfilePhoto($photo);
        $this->_view->photos = $this->_model->Photo->getPhotosByKey($photo['key_id'], $photo['type_id']);
        if ($this->_typeId == PHOTO_TYPE_USER) {
            $this->_view->mainPhotoUrl = User::getContextUser('photo_url');
        } else {
            $object = $this->_model->{$this->_objectName}->getById($this->_keyId);
            if (!empty($object)) {
                $this->_view->mainPhotoUrl = $object[$this->_objectPhotoUrl];
            }
        }
        $this->_view->getJSONResponse('photo/wItems');
        return JSON_OK;
    }

    private function _setProfilePhoto($photo)
    {
        if ($this->_typeId == PHOTO_TYPE_USER) {
            $_SESSION['user_photo_url'] = $photo['photo_url'];
        }
        $this->_model->photo->setProfilePhoto($photo);
        return JSON_OK;
    }

    public function renderRemovePhoto()
    {
        $photo['photo_id']  = $_POST['photo_id'];
        $photo['photo_url'] = $_POST['photo_url'];
        $this->_model->photo->deletePhoto($photo);
        return JSON_OK;
    }
}
