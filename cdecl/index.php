<?php
$server = $_SERVER['SERVER_NAME'];
if (isset($_REQUEST['q']) && is_string($_REQUEST['q'])) {
    include_once('run_cdecl.php');
	$query = str_replace('+', ' ', $_REQUEST['q']);
	$output = run_3_cdecl($query);
}
else {
   $default_queries = array(
      'int (*(*foo)(const void *))[3]' => 'declare foo as pointer to function (pointer to const void) returning pointer to array 3 of int',
      'char (*(*x())[5])()' => 'declare x as function returning pointer to array 5 of pointer to function returning char',
      'char (*(*x[3])())[5]' => 'declare x as array 3 of pointer to function returning pointer to array 5 of char',
      'char ** const * const x' => 'declare x as const pointer to const pointer to pointer to char',
      'char * const (*(* const bar)[5])(int )' => 'declare bar as const pointer to array 5 of pointer to function (int) returning const pointer to char'
      );
      $query = array_rand($default_queries);
      $output = $default_queries[$query];
      if (rand(0, 1) == 0) list($query, $output) = array($output, $query);
}

function make_permalink($query) {
	global $server;
	return "http://$server/?q=" . urlencode($query);	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<style type="text/css">

#examples {
	margin-top: 0px;
	margin-left: 20px;
	margin-bottom: -300px;
	height: 300px;
	position: relative;
	top: 10px;
}

#example_instructions {
	float: left;
	height: 200px;
	margin-right: 30px;
	font-size: 14pt;
}

#examples ul {
	list-style-type: none;
	line-height: 22px;
	position: relative;
	bottom: 2px;
	margin-top: 0px;
}

#examples ul a {
	color: #bbb;
	text-decoration: underline;
}

body {
	background-color: #152437;
	margin: 0px;
	color: #99b8bd;
}

#dark, #dark_top {
	background-color: #1c1c1c;
	width: 100%;
	z-index: -1;
	position: absolute;
	left: 0px;
	top: 0px;
}

#dark {
	height: 50%;
	top: 10px;
}

#dark_top {
	height: 30px;
}

h1, h2 {
	font-family: Monaco, "Lucida Console", Consolas, "Courier New", Courier, mono;
	margin: 0px;
	font-weight: normal;
}

h1 {
	font-size: 52px;
	text-indent: 120px;
	height: 69px;
}

h2 {
	font-size: 22px;
	text-indent: 125px;
	height: 32px;
}

#vert_spacer {
	height: 50%;
	width: 100%;
	margin: 0px;
	min-height: 220px;
}

#title {
	height: 400px;
	width: 100%;
	margin-top: -125px;
	position: relative;
	z-index: 2;
}

#curve {
	position: absolute;
	top: 50%;
	z-index:-1;
	margin-top: 10px;
}

span.arrow {
	font: 26px "Helvetica Neue", Arial, Helvetica, Geneva, sans-serif;
}

#searcher {
	font-family: Monaco, Consolas, "Courier New", Courier, mono;
	font-size: 14pt;
	margin-left: 140px;
	margin-top: 20px;
	width: 70%;
	min-width: 500px;
}

#text_result {
	font-family: Monaco, "Lucida Console", Consolas, "Courier New", Courier, mono;
	font-size: 16pt;
	margin-left: 180px;
	margin-right: 150px;
	margin-top: 20px;
}

#permalink {
	margin-left: 180px;
	margin-top: 10px;
	font: bold 10pt "Helvetica Neue", Arial, Helvetica, Geneva, sans-serif;
}

#permalink a {
	text-decoration: none;
	color: #666;
}

#spinner {
	position: relative;
	left: 135px;
	top: 20px;
	width: 34px;
	height: 34px;
	margin-bottom: -34px;
	display: none;
}

#encloser {
	width: 100%;
	height: 100%;
	margin: 0px;
	padding: 0px;
	min-height: 480px;
	position: relative;
}

#help {
	float: right;
	margin-right: 16px;
	position: relative;
	z-index:1;
	top: 10px;
	text-align: right;
	line-height: 1.3;
}

#help a {
	color: #99b8bd;
	text-decoration: underline;
}

#gluck {
	text-align: center;
	position: absolute;
	bottom: 32px;
	right: 0px;
	width: 100%;
	z-index: 10;
}

#attribution {
	position: absolute;
	bottom: 0px;
	right: 0px;
	padding-right: 5px;
	padding-bottom: 3px;
	text-align: right;
}

#attribution a {
	color: #99b8bd;
	text-decoration: underline;
}

</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/livesearch.js"></script>
<script type="text/javascript">
 $(document).ready(function(){
   var searcher = $("#searcher");
   searcher.livesearch({
	       searchCallback: searchFunction,
	       queryDelay: 250,
	       innerText: "Gibberish",
	       minimumSearchLength: 3
   });
 });
 
function fade(x, direction) {
  if (direction) x.fadeIn("fast");
  else x.fadeOut("fast");
}
 
var sLoadingCount = 0;
function changeLoading(dx) {
	sLoadingCount += dx;
	fade($("#spinner"), sLoadingCount > 0);
}

function compute_permalink(nonplussedTerm) {
    var plussedTerm = nonplussedTerm.replace(/ /g, "+");
	return "http://cdecl.ridiculousfish.com/?q=" + escape(plussedTerm);
}

function transition_text(obj, txt, permalinkObj, showPermalink) {
    if (txt == obj.text()) return;
    var speed = 200;
    permalinkObj.fadeOut(speed);
	obj.slideUp(speed, function(){
		obj.text(txt);
		obj.slideDown(speed);
        if (showPermalink) permalinkObj.fadeIn(speed);
	});
}

function searchFunction(searchTerm) {
   changeLoading(1);
   $.post("http://<?php print $server;?>/query.php", {q: searchTerm},
    function(data, error){
	   changeLoading(-1);
	   var requestSuccess = (error == "success");
	   var syntacticSuccess = (data.length > 0 && data != "syntax error");
       var text_to_insert = (requestSuccess ? data : "Error " + error);
       $("#permalink a").attr("href", compute_permalink(searchTerm));
       transition_text($("#text_result"), text_to_insert, $("#permalink"), syntacticSuccess);
    });
}

function demo(ind) {
	var selector = "#examples li:eq(" + ind + ") > a";
	var text = $(selector).text();
	$("#searcher").val(text);
	searchFunction(text);
}
</script>
  <title>cdecl: C gibberish &harr; English</title>
</head>
<body>

  <div id="encloser">
	  <div id="dark_top"></div>
	  <div id="dark"></div>
	  <div id="help">
	      <a href="/files/cdecl-blocks-2.5.tar.gz">Source Code</a>
	  </div>
	  <div id="examples">
		<div id="example_instructions">
		  Try these examples:
		</div>
		<ul>
		  <li><a href="javascript:demo(0)">int (*(*foo)(void ))[3]</a></li>
		  <li><a href="javascript:demo(1)">declare bar as volatile pointer to array 64 of const int</a></li>
		  <li><a href="javascript:demo(2)">cast foo into block(int, long long) returning double</a></li>

		</ul>
	  </div>
	  <div id="vert_spacer">&nbsp;</div>
	  <div id="title">
		<h1>cdecl</h1>
		<h2>C gibberish <span class="arrow">&harr;</span> English</h2><input id="searcher" name="hl" value="<?php print htmlentities($query); ?>">
		<div id="spinner">
		  <img src="images/spinner.gif" width="34" height="34">
		</div>
		<div id="text_result">
		  <?php print htmlentities($output); ?>
		</div>
		<p id="permalink"><a href="<?php print make_permalink($query); ?>">permalink</a></p>
		
	  </div>
	  <img id="curve" src="images/curve.png" name="curve">
	  
<div id="gluck">
	<script type="text/javascript"><!--
	google_ad_client = "pub-0174887920412973";
	/* my_cdecl_ads */
	google_ad_slot = "4120600923";
	google_ad_width = 728;
	google_ad_height = 90;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>
	  
  </div>

<div id="attribution">
by <a href="http://ridiculousfish.com">ridiculous_fish</a>
<a href="&#x6d;&#97;&#105;&#108;&#x74;&#111;&#58;&#x63;&#100;&#101;&#99;&#108;&#64;&#114;&#x69;&#100;&#105;&#99;&#117;&#x6c;&#111;&#x75;&#x73;&#102;&#x69;&#x73;&#x68;&#46;&#99;&#x6f;&#109;"><img src="images/mail_icon.gif" width=13 height=10 /></a>
</div>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try{ 
var pageTracker = _gat._getTracker("UA-15181739-1");
pageTracker._trackPageview();
} catch(err) {} 
</script>
</body>
</html>
