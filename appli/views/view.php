<html>
    <head>
        <base href="/" >
        <script type="text/javascript" src="MLink/appli/inc/jquery-1.11.1.min.js"></script>
        <LINK REL=StyleSheet HREF="MLink/appli/inc/styles.css" TYPE="text/css" MEDIA=screen>
        <link rel="icon" type="image/png" href="MLink/images/icone/Fav.png" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/appli/inc/chat/css/chat.css" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/appli/inc/chat/css/screen.css" />
        <script type="text/javascript" src="MLink/appli/inc/ajax/link.js"></script>
        <?php if($this->isJSActivated(JS_DATEPICKER)) : ?>
            <link rel="stylesheet" type="text/css" href="MLink/appli/inc/datepicker/jquery.datetimepicker.css"/ >
            <script type="text/javascript" src="MLink/appli/inc/datepicker/jquery.datetimepicker.js"></script>
            <script type="text/javascript" src="MLink/appli/inc/datepicker/datepicker.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_AUTOCOMPLETE))
        : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/autocomplete.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_ARTICLE)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/article.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_COVOIT)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/covoit.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_SCROLL_REFRESH)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/scrollRefresh.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_PHOTO)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/photo.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_TASTE)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/taste.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_FORUM)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/forum.js"></script>
        <?php endif; ?>
        <?php if($this->isJSActivated(JS_SEARCH)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/search.js"></script>
        <?php endif; ?>
            <link rel="stylesheet" type="text/css" href="MLink/appli/inc/growler/css/gritter.css" />
            <script type="text/javascript" src="http://www.google.com/jsapi"></script>
            <script type="text/javascript" src="MLink/appli/inc/growler/js/gritter.js"></script>
            <script>
            $.extend($.gritter.options, {
                position: 'bottom-right'
            });
            </script>
            <?php if(count($this->_growlerMessages) > 0) : ?>
                <?php foreach($this->_growlerMessages as $message) :?>
                    <?php echo $message; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php if($this->isJSActivated(JS_MODAL)) : ?>
            <link rel="stylesheet" type="text/css" href="MLink/appli/inc/modal/css/magnific-popup.css" />
            <script type="text/javascript" src="MLink/appli/inc/modal/js/jquery.magnific-popup.js"></script>
        <?php endif; ?>
        <!--[if lte IE 7]>
        <link type="text/css" rel="stylesheet" media="all" href="css/screen_ie.css" />
        <![endif]-->
        <script type="text/javascript" src="MLink/appli/inc/chat/js/chat.js"></script>
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
