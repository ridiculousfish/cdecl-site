<?php
include_once('run_cdecl.php');

if (! isset($_REQUEST['q']) || ! is_string($_REQUEST['q'])) {
   print "Bad query";
   exit(0);
}
$q = $_REQUEST['q'];
print run_3_cdecl($q);

?>
