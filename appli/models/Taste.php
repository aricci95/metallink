<?php

class Taste extends AppModel
{

    private $_tasteTypes = array(
        TASTE_TYPE_SERIE => 'series',
        TASTE_TYPE_FILM => 'films',
        TASTE_TYPE_BOOK => 'livres',
    );

    public function getTastes($userId = null)
    {
        $userId = (empty($userId)) ? $this->context->get('user_id') : $userId;
        $sql = "SELECT data
                FROM taste
    			WHERE user_id = ".$this->securize($userId);
        $result = $this->fetchOnly($sql);
        if (!empty($result['data'])) {
            $result['data'] = unserialize($result['data']);
            if (is_array($result['data']) && (count($result['data']) > 0)) {
                $types = $this->getTasteTypes();
                foreach ($types as $key => $type) {
                    if (is_array($result['data']) && !array_key_exists($type, $result['data'])) {
                        $result['data'][$type] = array();
                    }
                }
            } else {
                $result = null;
            }
        }
        return $result;
    }

    public function getTasteTypes()
    {
        return $this->_tasteTypes;
    }

    public function save($values)
    {
        $this->execute("DELETE FROM taste WHERE user_id = ".$this->context->get('user_id'));
        $sql = "INSERT INTO taste (user_id, data)
                VALUES ('" . $this->context->get('user_id') . "','" . serialize($values) . "');";
        $this->execute($sql);
    }
}
