<?php //created by Jason McHuff, http://www.rosecitytransit.org/
//SET THIS:
$captureDir = "/trunk-recorder/captureDir";

function processdir($dir) {
	chdir($dir);
	$output = "";
	foreach (glob("*.json") as $file) {
		$string = file_get_contents($file);
		$json_a = json_decode($string, true);
		$output .= substr($file,strrpos($file,"_")+1,-4).",".$json_a['start_time'].",".($json_a['stop_time']-$json_a['start_time']).",0,".$json_a['talkgroup'].",".$json_a['emergency'].",0,0,0,".(int)$json_a['freqList'][0]['freq']."|0|".(int)$json_a['freqList'][0]['error_count']."|".(int)$json_a['freqList'][0]['spike_count'];
		foreach ($json_a['srcList'] as $source) {
			$output .= ",".$source['src']."|".$source['time']."|".$source['pos'];
		}
		unset($source);
		$output .= PHP_EOL;
	}
	file_put_contents("call_log.csv", $output);
	unset($file, $output);
	foreach (glob("*", GLOB_ONLYDIR) as $innerdir)
		processdir($innerdir);
}

processdir($captureDir);
?>
