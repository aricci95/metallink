<?php
/**
 * jqSajax :: Sajax use with JQUERY
 * @version $1.0.1
 * By winoto (http://satoewarna.com) email:winoto@satoewarna.com @2008 
 * You can export PHPfunction, your personal function, your method, or your PHP Class
 * and call it and return value from your AJAX  Application
 * You can return String,Numeric,Array,Object and vice versa
 * See the readme for detail
 * 
 * I have built some rich application based on PHP and MySQL (i.e ERP, accounting, inventory, parking, 
 * academic information system, schedulling, portal, medical information system, etc).
 * I can customize it for You.
 * 
 * jQuery (http://jquery.com) copyrighted by Jhon Resig (http://ejhon.org)
 * SAJAX PHP BACKEND copyrighted by Thomas Lackner and ModernMethod (http://www.modernmethod.com/).
 * serialize and unserialized method original by: Arpad Ray (mailto:arpad@php.net)
 * for wide encoding support,you can use serialize/unserialize from http://www.phprpc.org/
 * you can use JSON class from http://www.JSON.org/json2.js to parse and stringify JSON
 * 
 **/
class jqSajax{

	var $version = '1.0';
	var $debug_mode = 0;
	var $target_id='';
	var $export_list = array();
	var $request_type = 'POST';
	var $js_has_been_shown=0;
	var $failure_redirect = '';
	var $friendly_url =0;
	//the friendly_url is usefull for GET, if httacess  is set to friendy_url and $friendly_url=0,
	//the result may be the default page (error query occured)
	var $as_method=1;//export function or method as jqSajax Method
	var $as_jquery_plugin=0;//export function or method as jQuery Plugin
	var $asincronous=1;//$asincronous=1 to force javascript wait the result from ajax
	var $use_json=1;//force to use json

	function jqSajax($jq=1,$asc=1,$json=1){
		if($jq==0){$this->as_method=0;$this->as_jquery_plugin=0;}
		if($jq==1){$this->as_jquery_plugin=1;$this->as_method=0;}
		if($jq==2){$this->as_method=1;$this->as_jquery_plugin=0;}
		$this->asincronous=$asc;
		$this->use_json=$json;
		//if($this->as_jquery_plugin)$this->as_method=0;
	}

	function getUri($uri='') {
		if($uri=='')$uri=$_SERVER["REQUEST_URI"];
		return $uri;
	}
	
	function checkJson(){
		if(function_exists('json_encode') && $this->use_json)return true;
		else return false;
	}
	
	function getJsRepr($value) {
		$value=serialize($value);
			// Escapes a string so it can be safely echo'ed out as Javascript
        	$value = str_replace(array('\\', "'"), array("\\\\", "\\'"), $value);
        	$value = preg_replace('#([\x00-\x1F])#e', '"\x" . sprintf("%02x", ord("\1"))', $value);
		$value = "'$value'";
		return $value;
	}

	function processClientReq() {
		$mode = "";
		$obj=isset($_REQUEST['rsobj'])?$_REQUEST['rsobj']:'';
		
		if (! empty($_GET["rs"]))
			$mode = "get";

		if (!empty($_POST["rs"]))
			$mode = "post";

		if (empty($mode))
			return;

		$target = "";

		if ($mode == "get") {
			// Bust cache in the head
			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
			header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			// always modified
			header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
			header ("Pragma: no-cache");                          // HTTP/1.0
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"]))
				$args = $_GET["rsargs"];
			else
				$args = array();
		}
		else {
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"]))
				$args = $_POST["rsargs"];
			else
				$args = array();
		}
		if($obj!='')$fname=$obj.'->'.$func_name;
		else $fname=$func_name;
		if (! in_array($fname, $this->export_list))
			echo "-:$func_name not callable";
		else {
			if(!$this->checkJson())echo "+:";
			//for PHP 5
			foreach($args as $key=>$value)$args[$key]=str_replace(array("\\\"", "\\'"), array('"', "'"), $value);
			if($obj=='')$result = call_user_func_array($func_name, $args);
			else{
				eval("global \$$obj;");
				foreach($args as $key=>$value)$arg[$key]='\''.$value.'\'';
				if(isset($arg))$arg2=implode(',',$arg);
				else $arg2=NULL;
				eval("\$result=\$$fname($arg2);");
			}			
			//echo "var res = " . trim($this->getJsValue($result)) . "; res;";
			if(!$this->checkJson())echo "var res = " . trim($this->getJsRepr($result)) . "; res;";
			else {
					if(is_string($result))echo json_encode('"'.addslashes($result).'"');
					else echo json_encode($result);
					//see http://www.json.org/js.html for better solution
				}
			//because we use php serialize/unserialize http://www.coolcode.cn/?p=171
			//echo "var res = '" . trim(sajax_esc($result)) . "'; res;";
		}
		exit;
	}

	function getCommonJs() {
		$t = strtoupper($this->request_type);
		if ($t != "" && $t != "GET" && $t != "POST")
			return "// Invalid type: $t.. \n\n";

		ob_start();
		?>
						
		var jqSajax = {
		debug_mode : <?php echo $this->debug_mode ? "true" : "false"; ?>,request_type : "<?php echo $t; ?>",
		target_id : <?php echo '\''.$this->target_id.'\''; ?>,failure_redirect : "<?php echo $this->failure_redirect; ?>",
		friendly_url : <?php echo $this->friendly_url ? "true" : "false"; ?>,requests: new Array(),data_return:"",

		debug:function(text) {if (this.debug_mode)alert(text);},	
		cancel: function() {for (var i = 0; i < this.requests.length; i++)this.requests[i].abort();},
		
		serialize:function(inp){
			var getType=function(inp){var type=typeof inp,match;if(type=='object'&&!inp){return'null'}if(type=="object"){if(!inp.constructor){return'object'}var cons=inp.constructor.toString();if(match=cons.match(/(\w+)\(/)){cons=match[1].toLowerCase()}var types=["boolean","number","string","array"];for(key in types){if(cons==types[key]){type=types[key];break}}}return type};var type=getType(inp);var val;switch(type){case"undefined":val="N";break;case"boolean":val="b:"+(inp?"1":"0");break;case"number":val=(Math.round(inp)==inp?"i":"d")+":"+inp;break;case"string":val="s:"+inp.length+":\""+inp+"\"";break;case"array":val="a";case"object":if(type=="object"){var objname=inp.constructor.toString().match(/(\w+)\(\)/);if(objname==undefined){return}objname[1]=this.serialize(objname[1]);val="O"+objname[1].substring(1,objname[1].length-1)}var count=0;var vals="";var okey;for(key in inp){okey=(key.match(/^[0-9]+$/)?parseInt(key):key);vals+=this.serialize(okey)+this.serialize(inp[key]);count++}val+=":"+count+":{"+vals+"}";break}if(type!="object"&&type!="array")val+=";";return val;
		},
		unserialize:function(inp){
			error=0;if(inp==""||inp.length<2){errormsg="";return}var val,kret,vret,cval;var type=inp.charAt(0);var cont=inp.substring(2);var size=0,divpos=0,endcont=0,rest="",next="";switch(type){case"N":if(inp.charAt(1)!=";"){errormsg=""}rest=cont;break;case"b":if(!/[01];/.test(cont.substring(0,2))){errormsg=""}val=(cont.charAt(0)=="1");rest=cont.substring(2);break;case"s":val="";divpos=cont.indexOf(":");if(divpos==-1){errormsg="";break}size=parseInt(cont.substring(0,divpos));if(size==0){if(cont.length-divpos<4){errormsg="";break}rest=cont.substring(divpos+4);break}if((cont.length-divpos-size)<4){errormsg="";break}if(cont.substring(divpos+2+size,divpos+4+size)!="\";"){errormsg=""}val=cont.substring(divpos+2,divpos+2+size);rest=cont.substring(divpos+4+size);break;case"i":case"d":var dotfound=0;for(var i=0;i<cont.length;i++){cval=cont.charAt(i);if(isNaN(parseInt(cval))&&!(type=="d"&&cval=="."&&!dotfound++)){endcont=i;break}}if(!endcont||cont.charAt(endcont)!=";"){errormsg=""}val=cont.substring(0,endcont);val=(type=="i"?parseInt(val):parseFloat(val));rest=cont.substring(endcont+1);break;case"a":if(cont.length<4){errormsg="";return}divpos=cont.indexOf(":",1);if(divpos==-1){errormsg="";return}size=parseInt(cont.substring(1*divpos,0));cont=cont.substring(divpos+2);val=new Array();if(cont.length<1){errormsg="";return}for(var i=0;i+1<size*2;i+=2){kret=this.unserialize(cont,1);if(error||kret[0]==undefined||kret[1]==""){errormsg="";return}vret=this.unserialize(kret[1],1);if(error){errormsg="";return}val[kret[0]]=vret[0];cont=vret[1]}if(cont.charAt(0)!="}"){errormsg="";return}rest=cont.substring(1);break;case"O":divpos=cont.indexOf(":");if(divpos==-1){errormsg="";return}size=parseInt(cont.substring(0,divpos));var objname=cont.substring(divpos+2,divpos+2+size);if(cont.substring(divpos+2+size,divpos+4+size)!="\":"){errormsg="";return}var objprops=this.unserialize("a:"+cont.substring(divpos+4+size),1);if(error){errormsg="";return}rest=objprops[1];var objout="function "+objname+"(){";for(key in objprops[0]){objout+="this['"+key+"']=objprops[0]['"+key+"'];"}objout+="}val=new "+objname+"();";eval(objout);break;default:errormsg=""}return(arguments.length==1?val:[val,rest]);
		},
		fhasilc:function(text){this.debug("the data return is: " + text);var txt = text.replace(/^\s*|\s*$/g,"");
			if(txt.substring(2,5)=='var')return this.unserialize(eval(txt.substring(2)));
		},
		fhasil:function(text){
			jqSajax.debug("PHP method/function Result is: \n"+text);
			<?php
			if($this->asincronous){
				if($this->checkJson()) echo 'jqSajax.data_return=eval(text);';
				else echo 'jqSajax.data_return=text;';
			}
			else {
				if($this->checkJson()) echo 'var data==eval(text);';
				else echo 'var data=jqSajax.fhasilc(text);';
				echo 'alert(\'Type: \'+typeof data +\', data:\'+data);';
			}
			?>
		},
		<?php if($this->as_method)echo $this->getCommonMethod(); ?>	
		do_call:function(func_name, args, obj) {
			var i,post_data,target_id,uri;	
			this.debug("in jqSajax.do_call()...\nRequest Type: " + this.request_type + "\nTarget Id: " + this.target_id);
			target_id = this.target_id;
			if (typeof(this.request_type) == "undefined" || this.request_type == "")this.request_type = "GET";
			uri = "<?php echo $this->getUri(); ?>";
			if (this.request_type == "GET") {
				if (uri.indexOf("?") == -1 && this.friendly_url==false)uri += "?rs=" + escape(func_name);
				else uri += "&rs=" + escape(func_name);
				uri += "&rst=" + escape(target_id);
				uri += "&rsrnd=" + new Date().getTime();
				uri += "&rsobj=" + obj;
				for (i = 0; i < args.length; i++)uri += "&rsargs[]=" + escape(args[i]);
				post_data = null;
			}
			else if (this.request_type == "POST") {
				post_data = "rs=" + escape(func_name);
				post_data += "&rst=" + escape(this.target_id);
				post_data += "&rsrnd=" + new Date().getTime();
				post_data += "&rsobj=" + obj;
				for (i = 0; i < args.length; i++)post_data = post_data + "&rsargs[]=" + escape(args[i]);				
			}
			else this.debug("Illegal request type: " + this.request_type);
			
			this.debug("Do requested page with JQUERY ajax...");
			this.debug("URI: "+uri);
			this.debug("Post Data: "+post_data);
			$.ajax({
				async:false,
				type: this.request_type,
				url	: uri,
				data: post_data,
				<?php if($this->checkJson()) echo "dataType:\"json\",\n";?>
				success:this.fhasil
			});			
		}	
		};

		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function showCommonJs() {
		echo $this->getCommonJs();
	}

	function getOneStub($func_name,$obj='') {
		ob_start();
		if($obj!='')$pobj=$obj.'_';
		else $pobj='';
		?>
		function x_<?php echo $pobj.$func_name; ?>(){jqSajax.do_call("<?php echo $func_name; ?>",x_<?php echo $pobj.$func_name; ?>.arguments,"<?php echo $obj; ?>");<?php if($this->asincronous)if($this->checkJson()) echo 'return jqSajax.data_return';else echo 'return jqSajax.fhasilc(jqSajax.data_return)';?>}
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function getOneMethod($func_name,$obj='') {
		ob_start();
		if($obj!='')$pobj=$obj.'_';
		else $pobj='';
		?>
		x_<?php echo $pobj.$func_name; ?>:function(){this.do_call("<?php echo $func_name; ?>",this.x_<?php echo $pobj.$func_name; ?>.arguments,"<?php echo $obj; ?>");<?php if($this->asincronous)if($this->checkJson()) echo 'return jqSajax.data_return';else echo 'return jqSajax.fhasilc(jqSajax.data_return)';?>},
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function getOneJquery($func_name,$obj='') {
		ob_start();
		if($obj!='')$pobj=$obj.'_';
		else $pobj='';
		?>
		$.x_<?php echo $pobj.$func_name; ?>=function(){jqSajax.do_call("<?php echo $func_name; ?>",$.x_<?php echo $pobj.$func_name; ?>.arguments,"<?php echo $obj; ?>");<?php if($this->asincronous)if($this->checkJson()) echo 'return jqSajax.data_return';else echo 'return jqSajax.fhasilc(jqSajax.data_return)';?>};
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function export() {
		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$this->export_list[] = func_get_arg($i);
		}
	}
	
	function exportObj() {
		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$name=func_get_arg($i);
			eval("global \$$name;");
			eval("\$method=get_class_methods(\$$name);");
			foreach($method as $value)$this->export_list[] = $name.'->'.$value;
		}
	}
	
	function getCommonMethod()
	{
		$html = "";
		foreach ($this->export_list as $func) {
			$dt=explode('->',$func);
			if(!isset($dt[1]))$html .= $this->getOneMethod($dt[0]);
			else $html .= $this->getOneMethod($dt[1],$dt[0]);
		}
		return $html;
	}
	
	function getJs()
	{
		$html = "";
		if($this->as_jquery_plugin)$this->as_method=0;
		if($this->as_method)$html .= $this->getCommonJs();
		else{
			if (! $this->js_has_been_shown) {
				$html .= $this->getCommonJs();
				$this->js_has_been_shown = 1;
			}
			foreach ($this->export_list as $func) {
				$dt=explode('->',$func);
				if($this->as_jquery_plugin){
					if(!isset($dt[1]))$html .= $this->getOneJquery($dt[0]);
					else $html .= $this->getOneJquery($dt[1],$dt[0]);
				}
				if(!$this->as_jquery_plugin && !$this->as_method){
					if(!isset($dt[1]))$html .= $this->getOneStub($dt[0]);
					else $html .= $this->getOneStub($dt[1],$dt[0]);
				}
			}
		}
		return $html;
	}

	function showJs()
	{
		echo $this->getJs();
	}
}
?>
