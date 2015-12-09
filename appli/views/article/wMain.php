<script>
$(function(){
    $('.test-popup-link').magnificPopup({
      items: [
      <?php foreach($this->photos as $photo) : ?>
          <?php echo " {
            src: 'MLink/photos/profile/".$photo['photo_url']."'
          }, ";
          ?>
      <?php endforeach; ?>
    ],
    gallery: {
      enabled: true
    },
    type: 'image'
    });
});
</script>
<?php $this->_helper->blackBoxOpen(); ?>
<a class="test-popup-link" href="MLink/photos/profile/<?php echo $this->article['art_photo_url']; ?>"><div class="profilePortrait" style="float:left;background-image:url(MLink/photos/profile/<?php echo $this->article['art_photo_url']; ?>);"></div></a>
    <div class="profileInfo" style="float:left;border:1px #D8D8D8 dotted;width:390px;height:272px;padding:10px;">
        <?php if($this->getContextUser('id') == $this->article['user_id']) : ?>
            <div style="text-align:right;">
                <a href="article/edit/<?php echo $this->article['art_id']; ?>">Editer l'article</a></br>
                <a href="photo/<?php echo PHOTO_TYPE_ARTICLE; ?>/<?php echo $this->article['art_id']; ?>">Editer les photos</a></br>
            </div>
        <?php endif; ?>
        <h1><?php echo $this->article['art_libel']; ?></h1>
        Mise en vente : <?php Tools::timeConvert($this->article['art_date']); ?><br/>
        <?php
                if(!empty($this->article['livre_poste'])) {
                    echo '<br/>';
                    echo '<span style="color:green;">';
                    echo '<img src="MLink/images/icone/signet_approuve.gif"> Livré par la poste';
                    echo '</span>';
                }
                if(!empty($this->article['livre_surplace'])) {
                    echo '<br/>';
                    echo '<span style="color:green;">';
                    echo '<img src="MLink/images/icone/signet_approuve.gif"> Vente en mains propres ('.$this->article['livre_surplace'].' km maximum)';
                    echo '</span>';
                }
                echo '<br/>';
                echo '<br/>';
                echo '<br/>';
                echo '<span style="font-size:18px;">';
                echo 'Prix : ';
                if(!empty($this->article['art_price'])) echo $this->article['art_price'].' euros';
                else echo 'à négocier';
                echo '</span>';
        ?>
    </div>
<?php $this->_helper->blackBoxClose(); ?>
<!-- DESCRIPTION -->
<?php if(!empty($this->article['art_description'])) : ?>
    <?php $this->_helper->blackBoxOpen(); ?>
    <?php if(!empty($this->article['user_city'])) : ?>
    <div style="float:left;width:340px;height:340px;">
        <iframe style="border:3px #808080 groove" width="289" height="340" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?q=<?php echo $this->article['user_city']; ?>&amp;oe=utf-8&amp;client=firefox-a&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo $this->article['user_city']; ?>,+France&amp;t=h&amp;vpsrc=6&amp;output=embed"></iframe><br />
    </div>
    <div style="float:left;width:375px;">
    <?php else : ?>
        <div>
    <?php endif; ?>
        <h2 class="profileInfo" style="text-align:center;">Description</h2>
        <?php echo stripcslashes($this->article['art_description']); ?>
    </div>
    <?php $this->_helper->blackBoxClose(); ?>
<?php endif; ?>
<!-- Vendeur -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h1>Vendeur</h1>
    <a href="profile/<?php echo $this->article['user_id']; ?>"><div class="profilePortrait" style="float:left;background-image:url(MLink/photos/profile/<?php echo $this->article['user_photo_url']; ?>);"></div></a>
    <div class="profileInfo" style="float:left;border:1px #D8D8D8 dotted;width:390px;height:272px;padding:10px;">
    <h1>
        <a href="profile/<?php echo $this->article['user_id'] ?>">
        <?php echo $this->article['user_login']; ?></a>
        <?php $this->_helper->showStatut($this->article['user_last_connexion'], true); ?>
        <div class="divLink" style="float:right;"><?php $this->render('link/wItem', array('user' => $this->article)); ?></div>
    </h1>
    <?php if (isset($this->article['age']) && $this->article['age'] < 2000) : ?>
        <?php echo $this->article['age'] . ' ans'; ?>
        <br/>
    <?php endif; ?>
    Dernière connexion : <?php Tools::timeConvert($this->article['user_last_connexion']); ?><br/>
    Ville :
    <?php if ($this->article['user_city'] != '') : ?><?php echo $this->article['user_city']; ?><?php endif;?>
    </div>
<?php $this->_helper->blackBoxClose(); ?>
