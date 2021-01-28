<?
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		header("Location: /admin");
		die();
	}
	//Make backup of file
	$file = file('../data.js');
	$filename = substr($file[0], 2, 16);
	copy("../data.js","../backup/" . $filename . ".js");
	//Write data.js
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
	if (!is_numeric($minutes) || $minutes < -60 || $minutes > 60 ) {
		$minutes = 0;
	}
	fwrite($myfile, "var minutesOffset = $minutes;\r\n");
	$seconds = filter_input(INPUT_POST, 'seconds');
	if (!is_numeric($seconds) || $seconds < -60 || $seconds > 60 ) {
		$seconds = 0;
	}
	fwrite($myfile, "var secondsOffset = $seconds;\r\n");
	fwrite($myfile, "var date = '". date("Y-m-d h:i") ."';\r\n");
	fwrite($myfile, "var editor = '". $_SERVER['PHP_AUTH_USER'] ."';\r\n");
	fclose($myfile);

	//Write rotation_lists.ini
	$inifile = fopen("../rotation_lists.ini", "w");
	fwrite($inifile, "#" . date("Y-m-d h:i") . "\r\n");
	for ($column = 1; $column <= 6; $column++) {
		switch ($column) {
			case 1: fwrite($inifile, "[T1-3 Standard]\r\n"); break;
			case 2: fwrite($inifile, "[T1-2 Hardcore]\r\n"); break;
			case 3: fwrite($inifile, "[T4-6 Standard]\r\n"); break;
			case 4: fwrite($inifile, "[T3-6 Hardcore]\r\n"); break;
			case 5: fwrite($inifile, "[T7-10 Standard]\r\n"); break;
			case 6: fwrite($inifile, "[T7-10 Hardcore]\r\n"); break;
		}
		for ($row = 1; $row <= 10; $row++) {
			$id = 'mission-' . $row . '-' . $column;
			$data = filter_input(INPUT_POST, $id);
			fwrite($inifile, $row."=". $data."\r\n");
		}
	}
	fclose($inifile);

	//Write json
	$jsonfile = fopen("../data.json", "w");
	fwrite($jsonfile, "{\r\n"); //Start
	fwrite($jsonfile, "	\"data\": {\r\n"); //Data class start
	fwrite($jsonfile, "		\"minutesOffset\": " . $minutes . ",\r\n");
	fwrite($jsonfile, "		\"secondsOffset\": " . $seconds . ",\r\n");
	fwrite($jsonfile, "		\"date\": \"" . date("Y-m-d H:i") . "\",\r\n");
	fwrite($jsonfile, "		\"editor\": \"" . $_SERVER['PHP_AUTH_USER'] . "\",\r\n");
	fwrite($jsonfile, "		\"missions\": [\r\n");
	for ($column = 1; $column <= 6; $column++) {
		fwrite($jsonfile, "			[");
		for ($row = 1; $row <= 10; $row++) {
			$id = 'mission-' . $row . '-' . $column;
			$data = filter_input(INPUT_POST, $id);
			if ($row > 1) {
				fwrite($jsonfile, ", ");
			}
			fwrite($jsonfile, "\"$data\"");
		}
		fwrite($jsonfile, "]");
		if ($column < 6) {
			fwrite($jsonfile, ",");
		}
		fwrite($jsonfile, "\r\n");
	}
	fwrite($jsonfile, "		]\r\n");
	fwrite($jsonfile, "	}\r\n"); //Data class end
	fwrite($jsonfile, "}\r\n"); //End
	fclose($jsonfile);
	//Done
	header("Location: /admin");
	die();
?>