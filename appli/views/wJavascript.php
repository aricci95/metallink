<script type="text/javascript" src="MLink/libraries/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="MLink/appli/js/link.js"></script>
<script type="text/javascript" src="MLink/libraries/chat/js/chat.js"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="MLink/libraries/growler/js/gritter.js"></script>
<script>
    $.extend($.gritter.options, {
        position: 'bottom-right'
    });
</script>
<?php if(count($this->_growlerMessages) > 0) :
    foreach($this->_growlerMessages as $message) :
        echo $message;
    endforeach;
endif;

if($this->isJSActivated(JS_DATEPICKER)) : ?>
    <link rel="stylesheet" type="text/css" href="MLink/libraries/datepicker/jquery.datetimepicker.css"/ >
    <script type="text/javascript" src="MLink/libraries/datepicker/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="MLink/libraries/datepicker/datepicker.js"></script>
<?php endif;
if($this->isJSActivated(JS_AUTOCOMPLETE)) : ?>
    <link rel="stylesheet" href="MLink/libraries/jquery-ui/jquery-ui.css">
    <script src="MLink/libraries/jquery-ui/jquery-ui.js"></script>
    <script src="MLink/appli/js/autocomplete.js"></script>
<?php endif;
if($this->isJSActivated(JS_ARTICLE)) : ?>
    <script type="text/javascript" src="MLink/appli/js/article.js"></script>
<?php endif;
if($this->isJSActivated(JS_COVOIT)) : ?>
    <script type="text/javascript" src="MLink/appli/js/covoit.js"></script>
<?php endif;
if($this->isJSActivated(JS_SCROLL_REFRESH)) : ?>
    <script type="text/javascript" src="MLink/appli/js/scrollRefresh.js"></script>
<?php endif;
if($this->isJSActivated(JS_PHOTO)) : ?>
    <script type="text/javascript" src="MLink/appli/js/photo.js"></script>
<?php endif;
if($this->isJSActivated(JS_TASTE)) : ?>
    <script type="text/javascript" src="MLink/appli/js/taste.js"></script>
<?php endif;
if($this->isJSActivated(JS_FORUM)) : ?>
    <script type="text/javascript" src="MLink/appli/js/forum.js"></script>
<?php endif;
if($this->isJSActivated(JS_SEARCH)) : ?>
    <script type="text/javascript" src="MLink/appli/js/search.js"></script>
<?php endif;
if($this->isJSActivated(JS_ANNONCE)) : ?>
    <script type="text/javascript" src="MLink/appli/js/annonce.js"></script>
<?php endif;
if($this->isJSActivated(JS_MODAL)) : ?>
    <link rel="stylesheet" type="text/css" href="MLink/libraries/modal/css/magnific-popup.css" />
    <script type="text/javascript" src="MLink/libraries/modal/js/jquery.magnific-popup.js"></script>
    <script type="text/javascript" src="MLink/appli/js/modal.js"></script>
<?php endif; ?>