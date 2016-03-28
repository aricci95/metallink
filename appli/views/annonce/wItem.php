<a href="profile/<?php echo $this->annonce['user_id']; ?>">
    <?php $photo = empty($this->annonce['user_photo_url']) ? 'unknowUser.jpg' : $this->annonce['user_photo_url']; ?>
    <div class="smallPortrait" style="float:left;background-image:url(MLink/photos/small/<?php echo $photo; ?>);"></div>
</a>
<div style="padding :37px;">
    <?php
        echo '<a class="popup blackLink" href="annonce/show/' . $this->annonce['annonce_id'] . '">' . Tools::maxLength($this->annonce['annonce_title'], 70) . '</a>';
    ?>
</div>
<?php if ($this->context->get('user_id') == $this->annonce['user_id']) : ?>
    <span style="float:right;">
        <a style="margin-top:-53px;float: right;margin-right:10px;" href="annonce/delete/<?php echo $this->annonce['annonce_id']; ?>" title="Supprimer"><img src="MLink/images/icone/delete.png" /></a>
    </span>
<?php endif; ?>
