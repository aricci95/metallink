<?php

class LinkService extends Service
{

    public function setContextUserLinks()
    {
        $userId = $this->context->get('user_id');

        $links = $this->model->link->getUserLinks($userId);

        // initialisation des valeurs
        $return = array('count' => array(LINK_STATUS_NONE => 0,
                                         LINK_STATUS_SENT => 0,
                                         LINK_STATUS_RECEIVED => 0,
                                         LINK_STATUS_ACCEPTED => 0,
                                         LINK_STATUS_BLACKLIST => 0,
                                         LINK_STATUS_BLACKLISTED => 0));

        foreach ($links as $key => $link) {
            $newKey = ($link['expediteur_id'] != $userId) ? $link['expediteur_id'] : $link['destinataire_id'];

            if ($link['status'] == LINK_STATUS_SENT) {
                $status = ($link['expediteur_id'] != $userId) ? LINK_STATUS_RECEIVED : LINK_STATUS_SENT;
                $return[(int) $newKey] = (int) $status;
                $return['count'][$status]++;
            } elseif ($link['status'] == LINK_STATUS_BLACKLIST) {
                $status = ($link['expediteur_id'] != $userId) ? LINK_STATUS_BLACKLISTED : LINK_STATUS_BLACKLIST;
                $return[(int) $newKey] = (int) $status;
                $return['count'][$status]++;
            } else {
                $return[(int) $newKey] = (int) $link['status'];
                $return['count'][$link['status']]++;
            }
        }

        ksort($return);

        $this->context->set('links', $return);

        $this->context->set('links_count_received', $return['count'][LINK_STATUS_RECEIVED]);
        $this->context->set('links_count_accepted', $return['count'][LINK_STATUS_ACCEPTED]);
        $this->context->set('links_count_blacklist', $return['count'][LINK_STATUS_BLACKLIST]);

        return $return;
    }

    public function linkTo($destinataire)
    {
        $links = $this->context->get('links');

        $links[$this->model->link->linkTo($destinataire)] = LINK_STATUS_SENT;

        if (!empty($destinataire['user_mail'])) {
            $message = 'Vous avez reçu une nouvelle demande de la part de '. $this->context->get('user_login') .' !';

            $this->get('mailer')->send($destinataire['user_mail'], 'Nouvelle demande sur MetalLink !', $message);
        }

        $this->context->set('links', $links);

        return true;
    }

    public function getLinkStatus($userId)
    {
        $links = $this->context->get('links');

        return !empty($links[$userId]) ? $links[$userId] : LINK_STATUS_NONE;
    }

    public function isLinked($destinataireId)
    {
        $contextId = $this->context->get('user_id');

        return ($this->getLinkStatus($destinataireId) == LINK_STATUS_ACCEPTED);
    }
}
