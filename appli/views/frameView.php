<html>
    <head>
        <?php if($this->isJSActivated(JS_FORUM)) : ?>
            <script type="text/javascript" src="MLink/appli/js/forum.js"></script>
        <?php endif; ?>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <?php if(!empty($this->_title)) : ?><h1><?php echo $this->_title; ?></h1><?php endif; ?>
        <?php include($this->getViewFileName()); ?>
    </body>
</html>