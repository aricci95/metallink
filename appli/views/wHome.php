<!-- Nouveaux utilisateurs -->
<?php $this->_helper->blackBoxOpen(); ?>
    <div class="divModule">
        <div class="fb-like" data-href="https://www.facebook.com/metallinkofficial/" data-width="740" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
    </div>
    <div class="divModule">
        <a href="http://www.emp-online.fr/band-merch/?wt_mc=pt.pp.bandmerch.283#Q713Cu213yxM" target="_blank" title="Bandmerch 728 x 90"><img src="MLink/images/728x90_bm.jpg" height="90" width="728" border="0" alt="Bandmerch 728 x 90" /></a>
    </div>
    <div class="divModule">
        <?php $this->_helper->printConcert($this->concert); ?>
    </div>
    <div class="divModule">
        <!-- Facebook discussion -->
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
    </div>
<?php $this->_helper->blackBoxClose(); ?>


