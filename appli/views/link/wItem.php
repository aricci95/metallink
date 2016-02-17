<?php
var_dump($this->context->get('user_id')); die;
    $contextUserId = $this->context->get('user_id');
    $linkStatus    = !empty($this->newStatus) ? $this->newStatus : Link::getStatus($this->user['user_id']);
?>
<?php if(!empty($contextUserId) && $linkStatus != LINK_STATUS_BLACKLISTED) : ?>
    <?php if($contextUserId != $this->user['user_id']) : ?>
        <div class="linkDestinataire" data-destinataire-id="<?php echo $this->user['user_id']; ?>"  data-destinataire-login="<?php echo $this->user['user_login']; ?>" data-destinataire-mail="<?php if(!empty($this->user['user_mail'])) echo $this->user['user_mail']; ?>" data-destinataire-photo="<?php echo $this->user['user_photo_url']; ?>">
                <?php if($linkStatus == LINK_STATUS_NONE) : ?>
                    <a href=""><img class="link" data-status="<?php echo LINK_STATUS_SENT; ?>" src="MLink/images/icone/link.png" title="Linker cette personne" /></a>
                <?php elseif($linkStatus == LINK_STATUS_ACCEPTED) : ?>
                    <?php  //$destinataireId = ($this->link['expediteur_id'] == $this->context->get('user_id')) $this->link['expediteur_id'] : $this->link['destinataire_id']; ?>
                    <a href="javascript:void(0)" onclick="javascript:chatWith('<?php echo $this->user['user_login']; ?>')"><img src="MLink/images/icone/chat.png"  title="Chatter" /></a>
                    <a href="message/<?php echo $this->user['user_id']; ?>"><img src="MLink/images/icone/message.png" title="Envoyer un message" />
                <?php elseif($linkStatus == LINK_STATUS_SENT) : ?>
                    <img src="MLink/images/icone/link_sent.png" title="Demande en attente" />
                <?php elseif($linkStatus == LINK_STATUS_RECEIVED) : ?>
                    <a href=""><img class="link" data-status="<?php echo LINK_STATUS_ACCEPTED; ?>" src="MLink/images/icone/link.png" title="Valider la demande" /></a>
                    <a href=""><img class="link" data-status="<?php echo LINK_STATUS_BLACKLIST; ?>" src="MLink/images/icone/blacklist.png" title="Refuser la demande" /></a>
                <?php elseif($linkStatus == LINK_STATUS_BLACKLIST) : ?>
                    <a href=""><img class="link" data-status="<?php echo LINK_STATUS_SENT; ?>" src="MLink/images/icone/link.png" title="Autoriser cette personne" /></a>
                <?php endif; ?>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>
