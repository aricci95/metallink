<div class="heading">
    <?php echo stripslashes($this->annonce['annonce_title']); ?>
</div>
<div style="margin-left:25px;margin-top:25px;text-align: left;width: 775px;">
    <div class="grey" style="height: 294px;margin-left: -25px;margin-top: -25px;">
        <?php if(!empty($this->annonce['photo_url'])) : ?>
            <div class="profilePortrait" style="float:left;background-image:url(MLink/photos/profile/<?php echo $this->annonce['photo_url']; ?>);"></div>
        <?php endif;?>
        <div class="shadow"></div>
        <div style="padding-left:10px;padding-right:10px;">
            Posté
            <?php
                echo Tools::timeConvert($this->annonce['annonce_date']);
                if ($this->context->get('user_id') == $this->annonce['user_id']) : ?>
                    <span style="float:right;">
                        <a href="profile/edit" title="Editer"><img src="MLink/images/icone/edit.png" /></a>
                    </span>
                <?php
                endif;
                echo '<br/><br/>';
                echo !empty($this->annonce['annonce_content']) ? nl2br($this->annonce['annonce_content']) : $this->annonce['annonce_title'];
            ?>
        </div>
    </div>
</div>
<div class="title">AUTEUR</div>
<div style="margin:25px;text-align: left;width: 775px;">
    <div class="grey" style="height: 294px;margin-left: -25px;margin-top: -25px;">
        <div class="shadow"></div>
        <div style="float:left;padding-left:10px;padding-right:10px;">
            <div style="color:rgb(35, 31, 32);font-size: 35px;letter-spacing:-2px;font-weight: bold;width:100%;">
                <?php echo strtoupper($this->annonce['user_login']); ?> <?php $this->_helper->showStatut($this->annonce['user_last_connexion'], true); ?>
            </div>
            <br/>
            <?php if (!empty($this->annonce['ville_nom_reel'])) : echo $this->annonce['ville_nom_reel'] . ' (' . $this->annonce['ville_code_postal'] . ')'; endif; ?>
            <br/>
            Dernière connexion <?php echo Tools::timeConvert($this->annonce['user_last_connexion']); ?>
        </div>
        <?php $photo = empty($this->annonce['user_photo_url']) ? 'unknowUser.jpg' : $this->annonce['user_photo_url']; ?>
        <a style="float:right;margin-top:-23px;margin-right:-9px;" href="MLink/photos/profile/<?php echo $photo; ?>">
            <div class="profilePortrait" style="float:left;background-image:url(MLink/photos/profile/<?php echo $photo; ?>);"></div>
        </a>
    </div>
</div>