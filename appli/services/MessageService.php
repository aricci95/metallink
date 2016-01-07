<?php

Class MessageService
{
    /**
     * Envoi un message via la messagerie
     *
     * @param  int $expediteur_id
     * @param  int $destinataire_id
     * @param  string $content
     * @return bool
     */
    public function send($expediteur_id, $destinataire_id, $content)
    {
        if (empty($expediteur_id) || empty($destinataire_id)) {
            $message = "<br/><br/>Valeurs en param�tres : <br/>";

            throw new Exception('Erreur lors de la sauvegarde du message, destinataire / expediteur manquant' . $message, ERROR_BEHAVIOR);
        }

        $content = nl2br(str_replace('\\', '', htmlentities($content, ENT_QUOTES, 'utf-8')));

        $message_data = array(
            'content' => $content,
            'expediteur_id' => $expediteur_id,
            'destinataire_id' => $destinataire_id,
            'date' => date('Y-m-d H:i:s'),
            'state_id' => MESSAGE_STATUS_SENT,
            'mailbox_id' => 1,
        );

        if (Message::insert($message_data)) {
            $destinataire = User::findById($destinataire_id, array('user_mail'));
            $message      = User::getContextUser('login').' vous a envoy� un nouveau message ! <a href="http://metallink.fr/message/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';

            return Mailer::send($destinataire['user_mail'], 'Nouveau message sur MetalLink !', $message);
        }
    }

    /**
     * Poste un message sur le forum global
     *
     * @param  string $message
     * @param  int $authorId
     * @return bool
     */
    public function post($message)
    {
        if (empty($message)) {
            throw new Exception('message vide', ERROR_BEHAVIOR);
        }

        $message = nl2br(str_replace('\\', '', htmlentities($message, ENT_QUOTES, 'utf-8')));

        $message_data = array(
            'content' => $message,
            'user_id' => User::getContextUser('id'),
            'date' => date('Y-m-d H:i:s'),
            'user_login' => User::getContextUser('login'),
        );

        return Forum::insert($message_data);
    }
}