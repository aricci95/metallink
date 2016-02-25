<!-- Nouveaux utilisateurs -->
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>Nouveaux utilisateurs</h2>
    <div class="fb-like" data-href="https://www.facebook.com/metallinkofficial/" data-width="740" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
    <br/>
    <br/>
    <?php foreach ($this->newUsers as $key => $value) {
        if ($this->context->get('role_id') > 0) $this->_helper->printUser($value);
        else $this->_helper->printUser($value);
    } ?>
<?php $this->_helper->blackBoxClose(); ?>

<!-- Recommandation concert -->
<?php $this->_helper->blackBoxOpen('maxWidth'); ?>
    <h2>Recommandation concert</h2>
    <?php if(!empty($this->concerts[0]['flyer_url'])) : ?>
        <div style="text-align:center;">
            <img style="max-width:720px;max-height:500px;" src="<?php echo $this->concerts[0]['flyer_url']; ?>"/>
        </div>
    <?php else : ?>
        <span style="float:left;margin:5px;"><img width="200px" height="200px" src="<?php echo $this->concerts[0]['flyer_url']; ?>" /></span>
        <?php //echo nl2br($this->concerts[0]['news_contenu']); ?>
    <?php endif; ?>
<?php $this->_helper->blackBoxClose(); ?>

<!-- COVOITURAGE -->
<?php /*
<?php $this->_helper->blackBoxOpen(); ?>
    <h2>Nouveau covoiturage</h2>
    <?php $this->render('covoit/wItems', array('elements' => $this->newCovoits)); ?>
<?php $this->_helper->blackBoxClose(); ?>
*/ ?>

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
    <div id="fb-root"></div>
    <script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '737628719706534',
          xfbml      : true,
          version    : 'v2.5'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/fr_FR/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
    </script>
    <div class="fb-comments" data-href="http://metallink.fr/" data-num-posts="3" data-width="700" data-colorscheme="dark"></div>
<?php $this->_helper->blackBoxClose(); ?>


