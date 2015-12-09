<?php
	require_once("jqSajax.class.php");
	class MyObj {
			var $name, $age;
			
			function MyObj($name='', $age='') {
				$this->name = $name;
				$this->age = $age;
			}
			function method1($d1,$d2){
				return 'The people say about: '.$d1.' and '.$d2;
			}
			function method2(){
				return 'The people see the date server: '.date('d-n-Y');
			}
	}

	function return_array() {
		return array("name" => "Tom", "age" => 26);
	}
	
	function return_object() {
		$o = new MyObj("Tom", 26);
		return $o;
	}
	
	function return_string() {
		return "Name: Tom / Age: 26<>''\"";
	}
	
	function return_int() {
		return 26;
	}
	
	function return_float() {
		return 26.25;
	}
	
	//$ajax=new jqSajax(0,1,1);
	$ajax=new jqSajax();
	$ajax->request_type = "POST";
	//$ajax->debug_mode = 1;
	//$ajax->friendly_url= 1;
	//$ajax->as_method=1;
	$ajax->export("return_array", "return_object", "return_string",	"return_int", "return_float");//export function
	$children=new MyObj();
	$ajax->export('children->method1');//export method
	$people=new MyObj();
	$ajax->exportObj('people');//export all method in object People
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
<script src="jquery-1.2.2.pack.js"></script>
<script src="json.js"></script>
<script>
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
//alert(JSON.parse(JSON.stringify('saya')));
function return_object(){
var data=$.x_return_object();
alert('tipe: '+typeof data);
alert('name: '+data['name']);
alert('age: '+data['age']);
}
function return_array(){
var data=$.x_return_array();
alert('tipe: '+typeof data);
alert('name: '+data['name']);
alert('age: '+data['age']);
}
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
<li><a href="http://satoewarna.com">Satoewarna</a></li><li><a href="index.php">Home</a></li><li><a href="example_jquery.php">Call as JQUERY Plugin</a></li><li><a href="example_jqsajax.php">Call as jqSajax method</a></li><li><a href="example_function.php">Call as javascript function</a></li><li><a href="doc.html">Docs</a></li><li><a href="job.html">Give me a Job</a></li><li><a href="http://satoewarna.com/jqsajax/download.html">download</a></li><li><a href="https://sourceforge.net/project/admin/donations.php?group_id=235778">Donate</a></li>
</ul>
</div>
<div id="content">
<h2>Demo of Type:</h2>
<button onClick="return_array();">Return as array (will become an object)</button>
<button onClick="return_object()">Return as object</button>
<button onClick="alert('Type: '+typeof $.x_return_string()+' Value: '+ $.x_return_string());">Return as string</button>
<button onClick="alert('Type: '+typeof $.x_return_int()+' Value: '+ $.x_return_int());">Return as int</button>
<button onClick="alert('Type: '+typeof $.x_return_float()+' Value: '+ $.x_return_float());">Return as float/double</button>
<p>&nbsp;</p>
<h2>Demo of function/method/object export</h2>
<button onClick="alert($.x_children_method1('The truth','The prophet'))">Call method1 from $children</button>
<button onClick="alert($.x_people_method2())">Call method1 from $people</button>
<div id="ajax_display"></div>
</div>
<div id="footer">
<h5>By <a  href="http://satoewarna.com/jqsajax">Winoto</a> >> Design by <a href="http://www.kricit.co.uk/elgunvis">Ashley Johnson</a></h5>
</div>
</div>
</body>
</html>

