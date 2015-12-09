<html>
    <head>
        <LINK REL=StyleSheet HREF="MLink/appli/incstyles.css" TYPE="text/css" MEDIA=screen>
        <link rel="icon" type="image/png" href="MLink/images/icone/Fav.png" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/appli/incchat/css/chat.css" />
        <link type="text/css" rel="stylesheet" media="all" href="MLink/appli/incchat/css/screen.css" />
        <script type="text/javascript" src="MLink/appli/incjquery-1.11.1.min.js"></script>
        <?php if($this->isJSActivated(JS_FORUM)) : ?>
            <script type="text/javascript" src="MLink/appli/inc/ajax/forum.js"></script>
        <?php endif; ?>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <?php if(!empty($this->_title)) : ?><h1><?php echo $this->_title; ?></h1><?php endif; ?>
        <?php include($this->getViewFileName()); ?>
    </body>
</html>