<?php //created by Jason McHuff, http://www.rosecitytransit.org/
include("live.config.php");
header("Cache-Control: no-cache, no-store, max-age=0, must-revalidate");
$fewer = "";
if (isset($_SERVER['QUERY_STRING'])) {
	$calls = explode("+",$_SERVER['QUERY_STRING']);
	if (end($calls) == "fewer")
		$fewer = "+".array_pop($calls);
}
if (!isset($calls[0]))
	$calls[0] = $default_system;
if (!isset($calls[3]))
	$calls[3] = date('Y-n-j');
if (!isset($calls[1]) || ($calls[1] == ""))
	$calls[1] = "ALL";
if (!isset($calls[2]))
	$calls[2] = -20;

$dir = $captureDir.$calls[0]."/".str_replace("-","/",$calls[3])."/";

if (file_exists($dir."call_log.csv")) {
	$filedata = file($dir."call_log.csv",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$firstcall = true;
	echo count($filedata);
	if ($calls[2] <= count($filedata))
		$filedata = array_slice($filedata, $calls[2], NULL, true);
	foreach ($filedata as $wholeline) {
		$allparts = explode(",", $wholeline);
		if (($allparts[4] == $calls[1]) || ($calls[1] == "ALL")) {
			echo "<div";
			if ($firstcall) {
				echo " id=\"newcalls\"";
					$firstcall = false; }
			if ($allparts[5] == "1") echo " class=\"e\"";
			echo "><span><a href=\"?".$calls[0]."+".$calls[1]."+".$allparts[0]."+".$calls[3].$fewer."\">#".$allparts[0]."</a></span><span>";
			echo date("H:i:s", $allparts[1])."</span><span>";
			echo $allparts[3]."s/".$allparts[2]."s</span><span>";
			if (!$fewer)
				echo "p".$allparts[6]."</span><span>";
			if (isset($talkgroups[$allparts[4]])) echo $talkgroups[$allparts[4]]; else echo $allparts[4];
			echo "</span><span>";
			$counter = 13;
			while($counter < count($allparts)) {
				$source = explode("|", $allparts[$counter]);
				echo "radio ".$source[0]." (@".$source[2]."s), ";
				$counter++
			}
			$file = $dir.$allparts[4]."-".$allparts[1]."_".$allparts[9]."-call_".$allparts[0].".".$filetype;
			if (!fewer) {
				echo "</span><span>".((int)$allparts[9]/1000000)." MHz (".$allparts[10]."len ".$allparts[11]."err ".$allparts[12]."spk)";
			}
			unset($section3, $freqs);
			if (file_exists($file))
				echo "</span><span><a href=\"".$file."\">".round(filesize($file) / 1024)."k</a>";
			echo "</span></div>";
		}
	}
	unset($wholeline);
}


elseif (file_exists($dir)) {
	chdir($dir);
	echo "00";
	$output = ""; $outputrows = array();
	foreach (glob("*.".$filetype) as $file) {
		$theparts = explode("-",substr($file,0,strpos($file,"_")));
		if (($theparts[0] == $calls[1]) || ($calls[1] == "ALL")) {
			$output .= "<div><span><a href=\"?".$calls[0]."+".$calls[1]."+".substr($theparts[2],5,-4)."+".$calls[3].$fewer."\">#".substr($theparts[2],5,-4)."</a></span><span>";
			$output .= date("H:i:s", substr($theparts[1],0,strpos($theparts[1],"_"))."</span><span>";
			if (isset($talkgroups[$allparts[0]])) $output .= $talkgroups[$allparts[0]]; else $output .= $allparts[0];
			$output .= "</span><span>";
			$outputrows[] = $output . "<a href=\"" . $dir . $file . "\">".round(filesize($file) / 1024)."k</a></span></div>"; }
	}
	sort($outputrows);
	foreach ($outputrows as $outputrow)
		echo $outputrow."
";
} elseif (!is_dir($captureDir.$default_system)) {
	echo "bad directory configuration: ".$captureDir.$default_system." not found";
}
else echo "00"; ?>
