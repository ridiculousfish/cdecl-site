<?php
function run_cdecl($str, &$output) {
	
	$cdecl_path = '/home/pammon/cdecl/cdecl';
	$descriptorspec=array(
		0=>array("pipe","r"),
		1=>array("pipe","w"),
		2=>array("pipe","w")
	);
	
	$process = proc_open($cdecl_path, $descriptorspec, $pipes);
	
	if (is_resource($process)) {
		list($in, $out, $err) = $pipes;
		fwrite($in, $str);
		fwrite($in, "\n");
		fclose($in);
		
		$output = stream_get_contents($out);
		fclose($out);
		
		$output .= stream_get_contents($err);
		fclose($err);
		
		$output = trim($output);
		
		$error_code = proc_close($process);
		
		return $error_code == 0 && $output != "syntax error";
	}
	else {
		$output = 'proc_open() failed';
		return false;
	}
}

function run_3_cdecl($q) {
   $output = '';
   $success = run_cdecl($q, $output);
   if (! $success) $success = run_cdecl('explain '.$q, $output);
   if (! $success) $success = run_cdecl('declare '.$q, $output);
   return $output;

}
?>
