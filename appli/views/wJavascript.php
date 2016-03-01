<script type="text/javascript" src="MLink/appli/inc/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="MLink/appli/inc/ajax/link.js"></script>
<script type="text/javascript" src="MLink/appli/inc/chat/js/chat.js"></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="MLink/appli/inc/growler/js/gritter.js"></script>
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
    <link rel="stylesheet" type="text/css" href="MLink/appli/inc/datepicker/jquery.datetimepicker.css"/ >
    <script type="text/javascript" src="MLink/appli/inc/datepicker/jquery.datetimepicker.js"></script>
    <script type="text/javascript" src="MLink/appli/inc/datepicker/datepicker.js"></script>
<?php endif;
if($this->isJSActivated(JS_AUTOCOMPLETE)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/autocomplete.js"></script>
<?php endif;
if($this->isJSActivated(JS_ARTICLE)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/article.js"></script>
<?php endif;
if($this->isJSActivated(JS_COVOIT)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/covoit.js"></script>
<?php endif; 
if($this->isJSActivated(JS_SCROLL_REFRESH)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/scrollRefresh.js"></script>
<?php endif; 
if($this->isJSActivated(JS_PHOTO)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/photo.js"></script>
<?php endif; 
if($this->isJSActivated(JS_TASTE)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/taste.js"></script>
<?php endif;
if($this->isJSActivated(JS_FORUM)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/forum.js"></script>
<?php endif;
if($this->isJSActivated(JS_SEARCH)) : ?>
    <script type="text/javascript" src="MLink/appli/inc/ajax/search.js"></script>
<?php endif;
if($this->isJSActivated(JS_MODAL)) : ?>
    <link rel="stylesheet" type="text/css" href="MLink/appli/inc/modal/css/magnific-popup.css" />
    <script type="text/javascript" src="MLink/appli/inc/modal/js/jquery.magnific-popup.js"></script>
<?php endif; ?>