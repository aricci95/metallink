<html>
    <head>
        <base href="/" >
        <LINK REL=StyleSheet HREF="MLink/appli/styles.css" TYPE="text/css" MEDIA=screen>
        <link rel="icon" type="image/png" href="MLink/images/icone/Fav.png" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/libraries/chat/css/chat.css" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/libraries/chat/css/screen.css" />
        <link rel="stylesheet" type="text/css" href="MLink/libraries/growler/css/gritter.css" />
        <!--[if lte IE 7]>
            <link type="text/css" rel="stylesheet" media="all" href="css/screen_ie.css" />
        <![endif]-->
        <?php $this->render('wJavascript'); ?>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>MetalLink</title>
    </head>
    <body style="body">
    <div class="site" >
        <div>
            <a href="home">
                <img src="<?php echo $this->headerImg ?>" />
            </a>
        </div>
        <div class="intitule">
        <span align="center">Communauté métal en tous genres</span>
        <span style="color:#B40404;margin-left:268px;"><?php echo 'v'.APP_VERSION ?></span>
        </div>
        <div class="divCorps" align="left">
        <?php if($this->context->get('user_id')) : ?>
            <div class="menu">
                <a class="menuLien" <?php echo ($this->page == 'profile') ? 'style="color:white;"' : ''; ?> href="profile/<?php echo $this->context->get('user_id'); ?>">Profil</a>
                <a class="menuLien" <?php echo ($this->page == 'member') ? 'style="color:white;"' : ''; ?> href="user">Membres</a>
                <a class="menuLien" <?php echo ($this->page == 'concert') ? 'style="color:white;"' : ''; ?> href="concert">Concerts</a>
                <a class="menuLien" <?php echo ($this->page == 'forum') ? 'style="color:white;"' : ''; ?> href="forum">Chat</a>
                <a class="menuLien" href="http://www.emp-online.fr/musique-cinema/les-essentiels/offres-speciales/?wt_mc=pt.pp.musiksale.283#Q1179C.G3Lkj" target="_blank">Shop</a>

                <?php if($this->context->get('role_id') == AUTH_LEVEL_SUPERADMIN) : ?>
                    <a class="menuLien" <?php echo ($this->page == 'admin') ? 'style="color:white;"' : ''; ?> href="admin">Admin</a></td>
                <?php endif; ?>

                <a class="menuLien" href="home/disconnect">Déconnecter</a>

                <span style="float: right;">
                    <a class="menuIcone" href="mailbox"><img src="MLink/images/icone/message.png" />
                        <b><?php echo $this->context->get('new_messages'); ?></b>
                    </a>
                    <a class="menuIcone" href="link/<?php echo LINK_STATUS_SENT; ?>">
                        <img src="MLink/images/icone/link.png" />
                        <b><?php echo $this->context->get('links_count_received'); ?></b>
                    </a>
                    <a class="menuIcone" href="link/<?php echo LINK_STATUS_ACCEPTED; ?>">
                        <img src="MLink/images/icone/link_accepted.png" />
                        <b><?php echo $this->context->get('links_count_accepted'); ?></b>
                    </a>
                    <a class="menuIcone" href="views">
                        <img src="MLink/images/icone/views.gif" />
                        <b><?php echo $this->context->get('views'); ?></b>
                    </a>
                    <a class="menuIcone" href="link/<?php echo LINK_STATUS_BLACKLIST; ?>">
                        <img src="MLink/images/icone/link_refuse.png" />
                        <b><?php echo $this->context->get('links_count_blacklist'); ?></b>
                    </a>
                </span>
            </div>
        <?php else : ?>
            <div class="menu">
                <form action="home/login" method="post">
                    Login : <input style="margin-left:5px;margin-right:5px;" name="user_login" size="4" />
                    Password : <input style="margin-left:5px;margin-right:5px;" name="user_pwd" type="password" size="4" />
                    <input type="submit" value="Connexion" />
                    <input type="button" onclick="window.location.href = 'MLink/libraries/socialauth/station.php';" class="facebookButton" value="Via Facebook" />
                    <span style="margin-right:10px;margin-left:10px;">
                    	<a href="subscribe">S'inscrire !</a>
                    </span>
                    <span style="margin-right:10px;">
                        <a href="lostpwd">Mot de passe oublié</a>
                    </span>
                    <label for="savepwd">Enregistrer</label> <input id="savepwd" name="savepwd" type="checkbox" />
                </form>
            </div>
        <?php endif;
            $this->_helper->whiteBoxMainOpen();

            if(!empty($this->_title)) echo '<h1>' . $this->_title . '</h1>';

            include($this->getViewFileName());

            $this->_helper->whiteBoxMainClose();
        ?>
        </div>
    </div>
    </body>
</html>
