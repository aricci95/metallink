<html>
    <head>
        <base href="/" >
        <LINK REL=StyleSheet HREF="MLink/appli/inc/styles.css" TYPE="text/css" MEDIA=screen>
        <link rel="icon" type="image/png" href="MLink/images/icone/Fav.png" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/appli/inc/chat/css/chat.css" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/appli/inc/chat/css/screen.css" />
        <link rel="stylesheet" type="text/css" href="MLink/appli/inc/growler/css/gritter.css" />
        <!--[if lte IE 7]>
            <link type="text/css" rel="stylesheet" media="all" href="css/screen_ie.css" />
        <![endif]-->
        <?php $this->render('wJavascript'); ?>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>MetalLink</title>
    </head>
    <body style="body">
    <div class="site" >
        <div><a href="home"><img src="<?php echo $this->headerImg ?>" /></a></div>
        <div class="intitule" align="center">Communauté métal en tous genres <span style="color:#B40404;margin-left:225px;"><?php echo 'v'.APP_VERSION ?></span></div>
        <div class="divCorps" align="left">
        <?php if($this->context->get('user_id')) : ?>
            <div class="divBarre">
                <table class="tableMenu">
                    <tr align="center">
                        <td><a class="aMenu" <?php echo ($this->page == 'profile') ? 'style="color:white;"' : ''; ?> href="profile/<?php echo $this->context->get('user_id'); ?>">Profil</a></td>
                        <td><a class="aMenu" <?php echo ($this->page == 'search') ? 'style="color:white;"' : ''; ?> href="search">Recherche</a></td>
                        <td><a class="aMenu" <?php echo ($this->page == 'forum') ? 'style="color:white;"' : ''; ?> href="forum">Forum</a></td>
                         <?php /*
                        <td><a class="aMenu" <?php echo ($this->page == 'sales') ? 'style="color:white;"' : ''; ?> href="sales">Ventes</a></td>
                        <td><a class="aMenu" <?php echo ($this->page == 'covoit') ? 'style="color:white;"' : ''; ?> href="covoit">Covoit'</a></td> */ ?>
                        <?php if($this->context->get('role_id') >= AUTH_LEVEL_ADMIN) : ?>
                            <?php /*
                            <td><a class="aMenu" <?php echo ($this->page == 'adminnews') ? 'style="color:white;"' : ''; ?> href="adminNews">News</a></td>
                            */ ?>
                            <?php if($this->context->get('role_id') == AUTH_LEVEL_SUPERADMIN) : ?>
                                <td><a class="aMenu" <?php echo ($this->page == 'admin') ? 'style="color:white;"' : ''; ?> href="admin">Admin</a></td>
                            <?php endif; ?>
                        <?php endif; ?>
                        <td><a class="aMenu" href="home/disconnect">Déconnecter</a></td>
                        <td align="right">
                            <a class="aMenu" href="mailbox"><img src="MLink/images/icone/message.png" /> <b><?php echo $this->context->get('new_messages'); ?></b></a>
                            <a class="aMenu" href="link/<?php echo LINK_STATUS_SENT; ?>"><img src="MLink/images/icone/link.png" /> <b><?php echo $this->context->get('links_count_received'); ?></b></a>
                            <a class="aMenu" href="link/<?php echo LINK_STATUS_ACCEPTED; ?>"><img src="MLink/images/icone/link_accepted.png" /> <b><?php echo $this->context->get('links_count_accepted'); ?></b></a>
                            <a class="aMenu" href="views"><img src="MLink/images/icone/views.gif" /> <b><?php echo $this->context->get('views'); ?></b></a>
                            <a class="aMenu" href="link/<?php echo LINK_STATUS_BLACKLIST; ?>"><img src="MLink/images/icone/link_refuse.png" /> <b><?php echo $this->context->get('links_count_blacklist'); ?></b></a>
                        </td>
                    </tr>
                </table>
            </div>
        <?php else : ?>
            <div class="divBarre">
                <form action="home/login" method="post">
                    <table class="tableMenu">
                        <tr>
                            <td>Login :</td><td><input name="user_login" size="8" /></td>
                            <td>Password :</td><td><input name="user_pwd" type="password" size="8" /></td>
                            <td><input type="submit" value="Connexion" /></td>
                            <td><input type="button" onclick="window.location.href = 'MLink/appli/inc/socialauth/station.php';" class="facebookButton" value="Via Facebook" /></td>
                            <td></td><td>Se souvenir de moi <input name="savepwd" type="checkbox" /></td>
                            <td></td><td><a href="lostpwd">Mot de passe oublié</a></td>
                            <td><a href="subscribe" >S'inscrire !</a></td>
                        </tr>
                    </table>
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
