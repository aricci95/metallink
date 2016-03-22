<div class="main minHeight">
    <div class="heading topShadow" align="center" style="height: 90px;padding: 9px;">

        <a href="http://www.emp-online.fr/band-merch/?wt_mc=pt.pp.bandmerch.283#Q713Cu213yxM" target="_blank" title="Bandmerch 728 x 90">
            <img src="MLink/images/728x90_bm.jpg" height="90" width="728" border="0" alt="Bandmerch 728 x 90" />
        </a>
        <div style="margin-top: -22px;margin-left: -9px;">
        <?php if (!empty($this->concert)) : ?>
              <?php $this->render('concert/wItem', $this->concert); ?>
        <?php endif; ?>
        </div>
    </div>


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
<?php /*
<div class="fb-comments" data-href="http://metallink.fr/" data-num-posts="3" data-width="700" data-colorscheme="dark"></div> */ ?>
<div id="fb-root"></div>
</div>
<div class="fb-like" data-href="https://www.facebook.com/metallinkofficial/" data-width="740" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>

