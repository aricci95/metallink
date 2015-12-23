<!-- Nouveaux utilisateurs -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>Nouveaux utilisateurs</h2>
    <div class="fb-like" data-href="https://www.facebook.com/pages/MetalLinkfr-Site-de-rencontre-pour-Metalheads/212049302188164?fref=ts" data-width="740" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
    <br/>
    <br/>
    <?php foreach ($this->newUsers as $key => $value) {
        if(isset($_SESSION['role_id']) && $_SESSION['role_id'] > 0) $this->_helper->printUser($value);
        else $this->_helper->printUser($value);
    } ?>
<?php $this->_helper->blackBoxClose(); ?>

<!-- News Etendue -->
<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
    <h2><?php echo stripslashes($this->decouverte['news_titre']); ?></h2>
    <?php if(empty($this->decouverte['news_contenu']) && !empty($this->decouverte['news_photo_url'])) : ?>
        <div style="text-align:center;">
            <img style="max-width:720px;max-height:500px;" src="<?php echo $this->decouverte['news_photo_url']; ?>"/>
        </div>
    <?php else : ?>
        <span style="float:left;margin:5px;"><img width="200px" height="200px" src="<?php echo $this->decouverte['news_photo_url']; ?>" /></span>
        <?php echo nl2br($this->decouverte['news_contenu']); ?>
    <?php endif; ?>
<?php $this->_helper->blackBoxClose(); ?>

<!-- COVOITURAGE -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>Nouveau covoiturage</h2>
    <?php $this->render('covoit/wItems', array('elements' => $this->newCovoits)); ?>
<?php $this->_helper->blackBoxClose(); ?>

<!-- ARTICLES -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>Nouveaux articles</h2>
    <?php foreach ($this->newArticles as $key => $value) $this->_helper->printArticle($value); ?>
<?php $this->_helper->blackBoxClose(); ?>

<!-- News list -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>News</h2>
    <ul>
    <?php foreach ($this->lesNews as $key => $value) : ?>
        <li>
            <a href="news/<?php echo $value['news_id']; ?>" ><b><u><?php echo Tools::toFrenchDate($value['news_date'], false); ?></u></b> :
            <?php echo $value['news_titre']; ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php $this->_helper->blackBoxClose(); ?>

<!-- Facebook discussion -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>Discussion générale</h2>
    <div id="fb-root"  style="margin-top:20px;"></div>
    <script>(function(d){
      var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
      js = d.createElement('script'); js.id = id; js.async = true;
      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
      d.getElementsByTagName('head')[0].appendChild(js);
    }(document));</script>
    <div class="fb-comments" data-href="http://metallink.fr/" data-num-posts="3" data-width="700" data-colorscheme="dark"></div>
<?php $this->_helper->blackBoxClose(); ?>


