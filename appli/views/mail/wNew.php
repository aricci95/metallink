<form action="mail/submit<?php if(!empty($_GET['value'])) : ?>/<?php echo $_GET['value']; ?><?php endif;?>" method="post">
    <?php if(!empty($this->parentMessages)) : ?>
        <input type="hidden" name="last_content" value="<?php echo $this->parentMessages[0]['content']; ?>" />
    <?php endif; ?>
    <div style="float:left;margin-top:20px;"><?php $this->_helper->printUserSmall($_SESSION);?></div>
    <div style="float:left;">
        A :
        <?php if(!empty($this->destinataire)) : ?>
            <a href="profile/<?php echo $this->destinataire['user_id']; ?>"><b><?php echo $this->destinataire['user_login']; ?></b></a> <?php echo $this->_helper->showStatut($this->destinataire['user_last_connexion']);?>
            <input type="hidden" value="<?php echo $this->destinataire['user_id']; ?>" name="destinataire" />
        <?php endif; ?>
        <br/>
        <textarea name="content" cols="100" rows="8"></textarea>
    </div>
    <br/>
    <?php $this->_helper->formFooter('mailbox'); ?>
</form>
