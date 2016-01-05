<?php

Class MessageService
{

    public function send($expediteur_id, $destinataire_id, $content)
    {
        if (empty($expediteur_id) || empty($destinataire_id)) {
            $message = "<br/><br/>Valeurs en paramètres : <br/>";

            throw new Exception('Erreur lors de la sauvegarde du message, destinataire / expediteur manquant' . $message, ERROR_BEHAVIOR);
        }

        $message_data = array(
            'content' => $content,
            'expediteur' => $expediteur_id,
            'destinataire' => $destinataire_id,
            'state_id' => MESSAGE_STATUS_SENT,
            'mailbox_id' => 1,
        );

        if (Message::insert($message_data)) {
            $destinataire = User::findById($destinataire_id, array('user_mail'));
            $message      = User::getContextUser('login').' vous a envoyé un nouveau message ! <a href="http://metallink.fr/message/' . User::getContextUser('id') . '">Cliquez ici</a> pour le lire.';

            return Mailer::send($destinataire['user_mail'], 'Nouveau message sur MetalLink !', $message);
        }
    }

    public function getMessages($expediteur_id = null, $destinataire_id = null)
    {

    }
}
