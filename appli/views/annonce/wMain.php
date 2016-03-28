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
            <?php
                echo !empty($this->annonce['annonce_content']) ? nl2br($this->annonce['annonce_content']) : $this->annonce['annonce_title'];
            ?>
        </div>
    </div>
</div>
<div class="title">AUTEUR</div>
<div style="margin:25px;text-align: left;width: 775px;">
    <div class="grey" style="height: 294px;margin-left: -25px;margin-top: -25px;">
        <?php $photo = empty($this->annonce['user_photo_url']) ? 'unknowUser.jpg' : $this->annonce['user_photo_url']; ?>
        <a class="test-popup-link" href="MLink/photos/profile/<?php echo $photo; ?>">
            <div class="profilePortrait" style="float:left;background-image:url(MLink/photos/profile/<?php echo $photo; ?>);"></div>
        </a>
        <div class="shadow"></div>
        <div style="padding-left:10px;padding-right:10px;">
            <div style="color:rgb(35, 31, 32);font-size: 35px;letter-spacing:-2px;font-weight: bold;width:100%;">
                <?php echo strtoupper($this->annonce['user_login']); ?> <?php $this->_helper->showStatut($this->annonce['user_last_connexion'], true); ?>
            </div>
            <br/>
            <?php if (!empty($this->annonce['ville_nom_reel'])) : echo $this->annonce['ville_nom_reel'] . ' (' . $this->annonce['ville_code_postal'] . ')'; endif; ?>
            <br/>
            Dernière connexion <?php Tools::timeConvert($this->annonce['user_last_connexion']); ?>
        </div>
    </div>
</div>