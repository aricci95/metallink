<h2><?php echo $this->currentNews['news_titre']; ?></h2>
<?php $this->_helper->blackBoxOpen(); ?>
    <div <?php if(empty($this->currentNews['news_contenu'])) echo 'align="center"'; ?>>
        <?php if(empty($this->currentNews['news_contenu']) && !empty($this->currentNews['news_photo_url'])) : ?>
            <img  style="max-width:720px;max-height:800px;" src="<?php echo $this->currentNews['news_photo_url']; ?>"/>
        <?php else : ?>
            <?php if ($this->currentNews['news_photo_url'] != '') : ?>
                <div style="float:left;margin:10px;">
                    <img width="250" heigth="250" src="<?php echo $this->currentNews['news_photo_url']; ?>"/>
                </div>
             <?php endif; ?>
            Le <b><?php echo $this->currentNews['news_date']; ?></b> par <b><a href="profile.php?id=<?php echo $this->newsAuteur['user_id']; ?>"><?php echo $this->newsAuteur['user_login']; ?></a></b>
            <?php echo nl2br(stripslashes($this->currentNews['news_contenu'])); ?>
            <?php if ($this->currentNews['news_media_url'] != '') : ?>
                <br/><br/>
                <iframe align="center" width="740px" height="400px" src="http://www.youtube.com/embed/<?php echo $this->currentNews['news_media_url']; ?>" frameborder="0" allowfullscreen></iframe>';
            <?php endif; ?>
        <?php endif;?>
    </div>
<?php $this->_helper->blackBoxClose(); ?>
