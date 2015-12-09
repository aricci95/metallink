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
	
	//$ajax=new jqSajax(0,1,1);
	$ajax=new jqSajax();
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
<title>jqSajax</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
@import "screen.css";
</style>
<script language="javascript" src="jquery-1.2.2.pack.js"></script><br />
<script language="javascript">
<?php
	$ajax->showJs();
?>


</script>
</head>
<body>
<div id="wrap">
<div id="header">
<div class="header">jqSajax</div>
<h1>Call your PHP method/function from Javascript</h1>
</div>
<div id="menu">
<ul>
<li><a href="http://satoewarna.com">Satoewarna</a></li><li><a href="index.php">Home</a></li><li><a href="example_jquery.php">Call as JQUERY Plugin</a></li><li><a href="example_method.php">Call as jqSajax method</a></li><li><a href="example_function.php">Call as javascript function</a></li><li><a href="doc.html">Docs</a></li><li><a href="job.html">Give me a Job</a></li><li><a href="http://satoewarna.com/jqsajax/download.html">download</a></li><li><a href="https://sourceforge.net/project/admin/donations.php?group_id=235778">Donate</a></li>
</ul>
</div>
<div id="content">
<h1>Welcome to jqSajax</h1>
<ul>
<li>You can call PHP method/function from javascript. You can use it as JQUERY plugin. so you can call your php method as <br /><span  class="sourcecode">var data=$.x_page_multiply(var1,var2);</span> multiply() is php method in page object. or <br />
<span  class="sourcecode">var data=$.x_multiply(var1,var2);</span> multiply() is php function</li>
<li>You can also call PHP method/function as javascript method.<br />
<span class="sourcecode">var data=jqSajax.x_page_multiply(var1,var2);</span> or <br /> <span class="sourcecode">var data=jqSajax.x_multiply(var1,var2);</span></li>
<li>You can also call php method/function as javascript function.<br />
<span class="sourcecode">var data=x_page_multiply(var1,var2);</span> or<br />
<span class="sourcecode">var data=x_multiply(var1,var2);</span></li>
<li>Get your AJAX result directy like <span class="sourcecode">var data=$.x_multiply(var1,var2);</span></li>
<li>You can pass all variable types: array,object,string,numeric from javascript to PHP and vice versa. See the <a href="example_types.php">Test</a></li>
<li>Inspired from <a href="http://jquery.com">JQUERY</a> and <a href="http://www.modernmethod.com">SAJAX</a></li>
<li>Please download <a href="http://satoewarna.com/jqsajax/download.html">latest Release</a></li>
</ul>

<h2>Example: call PHP method $page->multiply($var1,$var2) from Javascript</h2>
<form name="ftest">
<ul><li>
<input type="text" size="6" id="var1" name="var1" onchange="$('#result').val($.x_page_multiply($('#var1').val(),$('#var2').val()))" /> * <input type="text" size="6" id="var2" name="var2" onchange="$('#result').val($.x_page_multiply($('#var1').val(),$('#var2').val()))" /> = <input type="text" readonly="" id="result" name="result" size="20" /><input type="button" title="No action, just to triger onchange event in previous field" value="Count" /></li></ul>
</form>

<h2>How to install</h2>
<ol>
<li>include <a href="http://jquery.com">JQUERY Library</a> (1.2 or higher  is recomended) in your page.<br />
<span class="sourcecode"><pre>&lt;script src="jquery-1.2.2.pack.js"&gt;&lt;/script&gt;</pre></span></li>
<li>include jqSajax.class.php in your script<br />
<span class="sourcecode"><pre>require_once("jqSajax.class.php");</pre></span></li>
<li>Cutomize your preference and your PHP method/function. The method/function may be in defferent file.<br />
<span class="sourcecode">
<pre>
$ajax=new jqSajax();
$ajax->request_type = "POST";
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
var result=$.x_multiply(var1,var2);
</pre>
</span>
</li>
</ol>

<h2>Compatibility</h2>
<ul>
<li>You can use with other class like <a href="http://www.JSON.org/json2.js">JSON class</a> to parse and stringify JSON from javascript to PHP or vice versa.</li>
<li>You can use with other class like <a href="http://www.phprpc.org/">PHPSerializer class</a> to serialize and unserialize data from javascript to PHP or vice versa.</li>
</ul>
<div id="ajax_display"></div>
</div>
<div id="footer">
<h5>By <a  href="http://satoewarna.com/jqsajax">Winoto</a> >> Design by <a href="http://www.kricit.co.uk/elgunvis">Ashley Johnson</a></h5>
</div>
</div>
</body>
</html>