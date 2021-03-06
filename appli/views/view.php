<html>
    <head>
        <base href="/" >
        <?php $this->render('wJavascript'); ?>
        <link REL=StyleSheet HREF="MLink/appli/styles.css" TYPE="text/css" MEDIA=screen>
        <link rel="icon" type="image/png" href="MLink/images/icone/Fav.png" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/libraries/chat/css/chat.css" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/libraries/chat/css/screen.css" />
        <link rel="stylesheet" type="text/css" href="MLink/libraries/growler/css/gritter.css" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>MetalLink</title>
    </head>
    <body>
        <div class="userMenu">
            <div style="width:815px;margin-right:auto;margin-left:auto;">
                <?php if($this->context->get('user_id')) : ?>
                    <div style="float:left;width:90px;text-align: left;">
                        <a class="lienProfil" href="profile/<?php echo $this->context->get('user_id'); ?>">Mon profil</a>
                        <a href="auth/disconnect" class="greyLink" style="text-align: left;">Deconnexion</a>
                    </div>
                    <span style="float: right;font-size: 25px;">
                        <a class="menuIcone" href="mailbox"><img src="MLink/images/icone/message.png" />
                            <b><?php echo $this->context->get('new_messages'); ?></b>
                        </a>
                        <a class="menuIcone" href="link/<?php echo LINK_STATUS_SENT; ?>">
                            <img src="MLink/images/icone/link.png" />
                            <b><?php echo $this->context->get('links_count_received'); ?></b>
                        </a>
                        <a class="menuIcone" href="link/<?php echo LINK_STATUS_ACCEPTED; ?>">
                            <img src="MLink/images/icone/linked.png" />
                            <b><?php echo $this->context->get('links_count_accepted'); ?></b>
                        </a>
                        <a class="menuIcone" href="views">
                            <img src="MLink/images/icone/views.png" />
                            <b><?php echo $this->context->get('views'); ?></b>
                        </a>
                        <a class="menuIcone" href="link/<?php echo LINK_STATUS_BLACKLIST; ?>">
                            <img src="MLink/images/icone/blacklist.png" />
                            <b><?php echo $this->context->get('links_count_blacklist'); ?></b>
                        </a>
                    </span>
                <?php else : ?>
                    <div class="greyLink" style="font-family: Metallink;font-size:24px;color: white;">
                        <form action="auth/login" method="post">
                            Login : <input style="padding-top:-10px;margin-left:5px;margin-right:5px;" name="user_login" size="4" />
                            Password : <input style="margin-left:5px;" name="user_pwd" type="password" size="4" />
                            <input type="submit" value="Connexion" />
                            <input type="button" onclick="window.location.href = 'MLink/libraries/socialauth/station.php';" class="facebookButton" value="Via Facebook" />
                            <label style="margin-left:5px;" for="savepwd">Enregistrer</label><input id="savepwd" name="savepwd" type="checkbox" />
                            <a class="menuLien" style="margin-left:5px;"  href="lostpwd">Mot de passe oublie</a>
                            <a class="menuLien" style="margin:0;" href="subscribe">S'inscrire !</a>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div style="text-align: center;margin:20px;">
            <a href="user">
                <img src="MLink/images/structure/newheader.png" />
            </a>
        </div>
        <div class="site">
            <div class="menu">
                <a class="menuLien" <?php echo ($this->page == 'member') ? 'style="color:white;"' : ''; ?> href="user">Membres</a>
                <a class="menuLien" <?php echo ($this->page == 'concert') ? 'style="color:white;"' : ''; ?> href="concert">Concerts</a>
                <a class="menuLien" <?php echo ($this->page == 'forum') ? 'style="color:white;"' : ''; ?> href="forum">Chat</a>
                <?php /* <a class="menuLien" <?php echo ($this->page == 'annonces') ? 'style="color:white;"' : ''; ?> href="annonce">Annonces</a> */ ?>
                <a class="menuLien" href="http://www.emp-online.fr/musique-cinema/les-essentiels/offres-speciales/?wt_mc=pt.pp.musiksale.283#Q1179C.G3Lkj" target="_blank">Shop</a>
                <?php if($this->context->get('role_id') == AUTH_LEVEL_SUPERADMIN) : ?>
                    <a class="menuLien" <?php echo ($this->page == 'admin') ? 'style="color:white;"' : ''; ?> href="admin">Admin</a></td>
                <?php endif; ?>
            </div>
            <div style="min-height: 550px;">
                <?php include($this->getViewFileName()); ?>
            </div>
            <div class="footer">
                Réalisé par Antoine Ricci - aricci95@gmail.com<br/>Design par Laurianne Abbe - abbe.lauriane@gmail.com
            </div>
        </div>
    </body>
</html>
