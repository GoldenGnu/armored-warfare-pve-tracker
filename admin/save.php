<?
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		header("Location: /admin");
		die();
	}
	//Make backup of file
	$file = file('../data.js');
	$filename = substr($file[0], 2, 16);
	copy("../data.js","../backup/" . $filename . ".js");
	//Write new data
	$myfile = fopen("../data.js", "w");
	fwrite($myfile, "//" . date("Y-m-d_h-i") . "\r\n");
	fwrite($myfile, "var missions = [\r\n");
	for ($column = 1; $column <= 6; $column++) {
		fwrite($myfile, "	[");
		for ($row = 1; $row <= 10; $row++) {
			$id = 'mission-' . $row . '-' . $column;
			$data = filter_input(INPUT_POST, $id);
			if ($row > 1) {
				fwrite($myfile, ", ");
			}
			fwrite($myfile, "'$data'");
		}
		fwrite($myfile, "]");
		if ($column < 6) {
			fwrite($myfile, ",");
		}
		fwrite($myfile, "\r\n");
	}
	fwrite($myfile, "];\r\n");
	$minutes = filter_input(INPUT_POST, 'minutes');
	fwrite($myfile, "var minutesOffset = $minutes;\r\n");
	$seconds = filter_input(INPUT_POST, 'seconds');
	fwrite($myfile, "var secondsOffset = $seconds;\r\n");
	fwrite($myfile, "var date = '". date("Y-m-d h:i") ."';\r\n");
	fwrite($myfile, "var editor = '". $_SERVER['PHP_AUTH_USER'] ."';\r\n");
	fclose($myfile);

	//Done
	header("Location: /admin");
	die();
?>