<a href="profile/<?php echo $this->annonce['user_id']; ?>">
    <?php $photo = empty($this->annonce['user_photo_url']) ? 'unknowUser.jpg' : $this->annonce['user_photo_url']; ?>
    <div class="smallPortrait" style="float:left;background-image:url(MLink/photos/small/<?php echo $photo; ?>);"></div>
</a>
<div style="padding :37px;">
    <?php
        echo '<a class="popup blackLink" href="annonce/show/' . $this->annonce['annonce_id'] . '">' . Tools::maxLength($this->annonce['annonce_title'], 70) . '</a>';
    ?>
</div>
<?php if (!empty($this->annonce['photo_url'])) : ?>
    <div style="float:right;margin-top:-93px;margin-right: -9px;">
        <a href="profile/<?php echo $this->annonce['user_id']; ?>">
            <?php $photo = empty($this->annonce['user_photo_url']) ? 'unknowUser.jpg' : $this->annonce['user_photo_url']; ?>
            <div class="smallPortrait" style="float:left;background-image:url(MLink/photos/small/<?php echo $photo; ?>);"></div>
        </a>
    </div>
<?php endif; ?>
