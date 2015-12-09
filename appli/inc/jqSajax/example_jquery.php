<?php
	require_once("jqSajax.class.php");
	
	function multiply($var1,$var2){
		return $var1*$var2;
	}
	
	class myObj{
		function multiply($var1,$var2){
			return $var1*$var2;
		}	
	}
	$page=new  myObj();
	
	//$ajax=new jqSajax(1,1,1); or can be declared as $ajax=new jqSajax();
	$ajax=new jqSajax();//the default jqSajax(1,1,1)
	//$ajax->request_type = "POST";
	//$ajax->debug_mode = 1;
	//$ajax->friendly_url= 1;
	//$ajax->as_method=1;
	$ajax->export("multiply", "page->multiply");//export function
	$ajax->processClientReq();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>jqSajax: call as jQuery Plugin</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
@import "screen.css";
</style>
<script language="javascript" src="jquery-1.2.2.pack.js"></script><br />
<script language="javascript">
<?php
	$ajax->showJs();
?>
//show animation
$(function(){
	$("#ajax_display").ajaxStart(function(){
		$(this).html('<div style="position:absolute;top:'+400+'px;left:'+400+'px;"><p align=center><strong>Loading....</strong><br /><img src="ajax-loader.gif" /></p></div>');
	});
	$("#ajax_display").ajaxSuccess(function(){
   		$(this).html('');
 	});
	$("#ajax_display").ajaxError(function(url){
   		alert('jqSajax is error ');
 	});
});
</script>
</head>
<body>
<div id="wrap">
<div id="header">
<div class="header">jqSajax</div>
<h2>Example: call PHP method $page->multiply($var1,$var2) from Javascript</h2>
</div>
<div id="menu">
<ul>
<li><a href="http://satoewarna.com">Satoewarna</a></li><li><a href="index.php">Home</a></li><li><a href="example_jquery.php">Call as JQUERY Plugin</a></li><li><a href="example_method.php">Call as jqSajax method</a></li><li><a href="example_function.php">Call as javascript function</a></li><li><a href="doc.html">Docs</a></li><li><a href="job.html">Give me a Job</a></li><li><a href="http://satoewarna.com/jqsajax/download.html">download</a></li><li><a href="https://sourceforge.net/project/admin/donations.php?group_id=235778">Donate</a></li>
</ul>
</div>
<div id="content">
<h2>Example: call PHP method <span class="sourcecode">$page->multiply($var1,$var2);</span> as jQuery Plugin <span class="sourcecode">var data=$.x_page_multiply(var1,var2);</span></h2>
<form name="ftest">
<ul><li>
<input type="text" size="6" id="var1" name="var1" onchange="$('#result').val($.x_page_multiply($('#var1').val(),$('#var2').val()))" /> * <input type="text" size="6" id="var2" name="var2" onchange="$('#result').val($.x_page_multiply($('#var1').val(),$('#var2').val()))" /> = <input type="text" readonly="" id="result" name="result" size="20" /> <input type="button" title="No action, just to triger onchange event in previous field" value="Count" /></li></ul>
</form>
<h2>How to install as jQuery Plugin call</h2>
<ol>
<li>include <a href="http://jquery.com">JQUERY Library</a> (1.2 or higher  is recomended) in your page.<br />
<span class="sourcecode"><pre>&lt;script src="jquery-1.2.2.pack.js"&gt;&lt;/script&gt;</pre></span></li>
<li>include jqSajax.class.php in your script<br />
<span class="sourcecode"><pre>require_once("jqSajax.class.php");</pre></span></li>
<li>Cutomize your preference and your method/function<br />
<span class="sourcecode">
<pre>
	function multiply($var1,$var2){
		return $var1*$var2;
	}
	
	class myObj{
		function multiply($var1,$var2){
			return $var1*$var2;
		}	
	}
	$page=new  myObj();
	
	//$ajax=new jqSajax(1,1,1); or can be declared as $ajax=new jqSajax();
	$ajax=new jqSajax();//the default jqSajax(1,1,1)
	//$ajax->request_type = "POST";
	//$ajax->debug_mode = 1;
	//$ajax->friendly_url= 1;
	//$ajax->as_method=1;
	
</pre>
</span>
</li>
<li>Export your php method/function<br />
<span class="sourcecode">
<pre>
$ajax->export("page->multiply", "multiply");
$ajax->processClientReq();
</pre>
</span>
</li>
<li>Print Javascript representation<br />
<span class="sourcecode">
<pre>
&lt;script language="javascript" &gt;
&lt;?php
	$ajax->showJs();
?&gt;

&lt;/script&gt;
</pre></span>
</li>
<li>Call it from javascript and get the result. You can customize the result.<br />
<span class="sourcecode">
<pre>
var data=$.x_page_multiply(var1,var2);
</pre>OR<pre>
var result=$.x_multiply(var1,var2);
</pre>
</span>
</li>
</ol>
<div id="ajax_display"></div>
</div>
<div id="footer">
<h5>By <a  href="http://satoewarna.com/jqsajax">Winoto</a> >> Design by <a href="http://www.kricit.co.uk/elgunvis">Ashley Johnson</a></h5>
</div>
</div>
</body>
</html>