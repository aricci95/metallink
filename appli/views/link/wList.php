<div class="main minHeight" style="margin-top: -31px;">
    <?php if($this->status == LINK_STATUS_ACCEPTED) : ?>
        <div class="title form">DEMANDES ACCEPTEES</div>
        <div class="shadow"></div>
        <div align="center" class="maxWidth results">
            <?php if(empty($this->users)) : ?>Aucune demande acceptée.
            <?php else : ?>
                <?php $this->render('user/wItems', array('elements' => $this->users)); ?>
                <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_ACCEPTED; ?>" />
            <?php endif; ?>
        </div>
    <?php elseif($this->status == LINK_STATUS_BLACKLIST) : ?>
        <div class="title form">UTILISATEURS IGNORES</div>
        <div class="shadow"></div>
        <div align="center" class="maxWidth results blacklist">
            <?php if(empty($this->users)) : ?>Aucun utilisateur ignoré.
            <?php else : ?>
                <?php $this->render('user/wItems', array('elements' => $this->users)); ?>
                <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_BLACKLIST; ?>" />
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="title form">DEMANDES RECUES</div>
        <div class="shadow"></div>
        <div align="center" class="maxWidth received">
            <?php if(empty($this->users['received'])) : ?>Aucune demande reçue.
            <?php else : ?>
                <?php   $this->render('user/wItems', array('elements' => $this->users['received'])); ?>
            <?php endif; ?>
        </div>
        <div class="title">UTILISATEURS ENVOYEES</div>
        <div class="shadow"></div>
        <div align="center" class="maxWidth results">
            <?php if(empty($this->users['sent'])) : ?>Aucune demande envoyée.
            <?php else : ?>
                <?php $this->render('user/wItems', array('elements' => $this->users['sent'])); ?>
                <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_SENT; ?>" />
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
