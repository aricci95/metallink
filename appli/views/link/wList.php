<?php if($this->status == LINK_STATUS_ACCEPTED) : ?>
    <div class="heading topShadow">DEMANDES ACCEPTEES</div>
    <?php if(empty($this->elements)) : ?>
        <div align="center" class="noresults">
            Aucune demande acceptée.
        </div>
    <?php else : ?>
        <div align="center" class="results">
            <?php $this->render('user/wItems', array('elements' => $this->elements)); ?>
            <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_ACCEPTED; ?>" />
        </div>
    <?php endif; ?>
<?php elseif($this->status == LINK_STATUS_BLACKLIST) : ?>
    <div class="heading topShadow">UTILISATEURS IGNORES</div>
    <?php if(empty($this->elements)) : ?>
        <div align="center" class="noresults">
            Aucun utilisateur ignoré.
        </div>
    <?php else : ?>
        <div align="center" class="results">
            <?php $this->render('user/wItems', array('elements' => $this->elements)); ?>
            <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_BLACKLIST; ?>" />
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="heading topShadow">DEMANDES RECUES</div>
    <?php if(empty($this->received)) : ?>
        <div align="center" class="noresults">
            Aucune demande reçue.
        </div>
    <?php else : ?>
        <div align="center" class="received">
            <?php $this->render('user/wItems', array('elements' => $this->received)); ?>
        </div>
    <?php endif; ?>
    <div class="title topShadow">UTILISATEURS ENVOYEES</div>
    <?php if(empty($this->sent)) : ?>
        <div align="center" class="noresults">
            Aucune demande envoyée.
        </div>
    <?php else : ?>
        <div align="center" class="sent">
            <?php $this->render('user/wItems', array('elements' => $this->sent)); ?>
            <img class="loading" src="MLink/appli/js/loading.gif" style="display:none;" data-show="false" data-end="false" data-offset="0" data-href="link" data-option="<?php echo LINK_STATUS_SENT; ?>" />
        </div>
    <?php endif; ?>
<?php endif; ?>
