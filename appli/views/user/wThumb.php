<?php
    $imageUrl = ((!empty($this->user['user_photo_url']) && file_exists($_SERVER["DOCUMENT_ROOT"] . "/MLink/photos/small/" . $this->user['user_photo_url']))) ? $this->user['user_photo_url'] : 'unknowUser.jpg';
?>
<a href="profile/<?php echo $this->user['user_id']; ?>" >
    <div class="divElementSmall">
        <div class="divPhoto" style="background:url(/MLink/photos/small/<?php echo $imageUrl; ?>);background-position: top center;">
            <img class="pictoStatus" src="MLink/images/icone/<?php echo Tools::status($this->user['user_last_connexion']); ?>" />
        </div>
        <span class="userFont" style="color:<?php echo ($this->user['user_gender'] == 1)  ? '#3333CC' : '#CC0000'; ?>">
            <?php echo Tools::maxLength($this->user['user_login'], 14); ?>
        </span>
    </div>
</a>