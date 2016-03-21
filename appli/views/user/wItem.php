<?php
    $imageUrl = ((!empty($this->user['user_photo_url']) && file_exists($_SERVER["DOCUMENT_ROOT"]."/MLink/photos/small/".$this->user['user_photo_url']))) ? $this->user['user_photo_url'] : 'unknowUser.jpg';
?>
<div class="divElement">
    <a href="profile/<?php echo $this->user['user_id']; ?>" >
    <div class="divPhoto" style="background:url('/MLink/photos/small/<?php echo $imageUrl; ?>');background-position: top center;">
        <img class="pictoStatus" src="MLink/images/icone/<?php echo Tools::status($this->user['user_last_connexion']); ?>" />
    </div>
    <div class="divInfo">
        <div class="userFont" style="float:left;margin-right:100px;color:<?php echo ($this->user['user_gender'] == 1) ? '#3333CC' : '#CC0000'; ?>" >
            <?php echo Tools::maxLength($this->user['user_login'], 13); ?>
        </div>
        <?php
            echo (isset($this->user['age']) && $this->user['age'] < 2000) ? '<br/>' . $this->user['age'].' ans' : '';
            echo !empty($this->user['ville_nom_reel']) ? '<br/>' . $this->user['ville_nom_reel'] : '';
            echo !empty($this->user['look_libel']) ? '<br/>' . $this->user['look_libel'] : '';
        ?>
        <div class="divLink" style="position:absolute;bottom:1;left:3;">
            <?php $this->render('link/wItem', array('user' => $this->user)); ?>
        </div>
    </div>
    </a>
</div>