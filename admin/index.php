<?
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		//Make backup of file
		$file = file('../data.js');
		$filename = substr($file[0], 2, 16);
		copy("../data.js", "../backup/" . $filename . ".js");
		//Write data.js
		$myfile = fopen("../data.js", "w");
		fwrite($myfile, "//" . date("Y-m-d_H-i") . "\r\n");
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
		if (!is_numeric($minutes) || $minutes < -60 || $minutes > 60) {
			$minutes = 0;
		}
		fwrite($myfile, "var minutesOffset = $minutes;\r\n");
		$seconds = filter_input(INPUT_POST, 'seconds');
		if (!is_numeric($seconds) || $seconds < -60 || $seconds > 60) {
			$seconds = 0;
		}
		fwrite($myfile, "var secondsOffset = $seconds;\r\n");
		fwrite($myfile, "var date = '" . date("Y-m-d H:i") . "';\r\n");
		fwrite($myfile, "var editor = '" . $_SERVER['PHP_AUTH_USER'] . "';\r\n");
		fclose($myfile);

		//Write rotation_lists.ini
		$inifile = fopen("../rotation_lists.ini", "w");
		fwrite($inifile, "#" . date("Y-m-d H:i") . "\r\n");
		for ($column = 1; $column <= 6; $column++) {
			switch ($column) {
				case 1: fwrite($inifile, "[T1-3 Standard]\r\n");
					break;
				case 2: fwrite($inifile, "[T1-2 Hardcore]\r\n");
					break;
				case 3: fwrite($inifile, "[T4-6 Standard]\r\n");
					break;
				case 4: fwrite($inifile, "[T3-6 Hardcore]\r\n");
					break;
				case 5: fwrite($inifile, "[T7-10 Standard]\r\n");
					break;
				case 6: fwrite($inifile, "[T7-10 Hardcore]\r\n");
					break;
			}
			for ($row = 1; $row <= 10; $row++) {
				$id = 'mission-' . $row . '-' . $column;
				$data = filter_input(INPUT_POST, $id);
				fwrite($inifile, $row . "=" . $data . "\r\n");
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
	}
?><!DOCTYPE html>
<html lang="en">
<head>
	<title>Admin</title>
	<meta charset="UTF-8">
	<link id="favicon" rel="shortcut icon" type="image/png" href=" data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAACf0lEQVQ4jY2Ty08TYRTFz/2+mWk7Q2lpyktAHgFCjGh8xbhk4YLEtTujLl36T0g0JiYm/gmuXbhzgSYuxAhEMCYYRFBehWKbTtt5fvNdV1USUDnbm/vLuTfnAP8X/Wsoj90gIikNQ0opAfCJ6UIIYZiW1TUwOH724uW7hhSdbr2+VN4tfTwoba+6lZ/7cRgEWmt9BJC2HWfs3KWpkeGhe4VMNOWkOWPbKUSRQqSYvQAuzPzmyuq35+9fv3rIzAwABgAIKeX0zVtPJ8cGbxvkS0pcpAxGLt8OyzIRhoqyxfHc/OJqznFK1w67NgAAzJwkSvtBLIvdp9HT24dc1sRIbxamoRCoLGYezGBpfg6ZYn/jCICZ2TbERGlzDV+W5zA6MYnJC1exlXJgSgtPZu6jerAP2zahwVUhhJUkSQSAZevrmUKXFdb2b3R35qCCBrbWPqNarcGPgE8LbyAEADDqXjxfKZdXmDkkIiVaDgLP25RWG3oGz4CkifZCEW6tCrfmghMNBmA7FrPWXUR0HsAwM2eN1i21Snmn4KSxuLCIOIrQltlG38Awyvt7COMYliVgZ1JaSu7WWncDqACoihYg9H03CgPPMAyo0EUmbaJWc7Gz/QNxHEMQkGvPa51oArMPYAdA7TcArNE/Mnawt72OjrwN3wvQbDRwUC5DxQpKKbB0gq2N9Q/M/BbAdyJqyj8OvKZI2dTX23klCryM7wcACbiNEEGzglxHj56dffey2ag/IqINAAEAPhJlK5XuOTU0cndwoPeOkzZHv66XkM+KZHlp5ZnXbDwmol1mVsd24RBICinb03bbtBTiut9szCoVv2DmZivCJxURkSCiv1b6F/mJMFVr2FOjAAAAAElFTkSuQmCC" />
	<script src="https://aw.nikr.net/data.js"></script>
	<script>
		function startUpdate() {
			updateTime();
			var intervalId = window.setInterval(function(){
				updateTime();
			}, 1000);
		}
		function updateTime() {
			var minutesOffset = document.getElementById("minutes").value;
			var secondsOffset = document.getElementById("seconds").value;
			var d = new Date();
			var seconds = (d.getUTCMinutes() * 60) + d.getUTCSeconds() - secondsOffset - (minutesOffset * 60);
			if (seconds > 1800)  {
				seconds = seconds - 1800;
			} else if (seconds <= 0) {
				seconds = seconds + 1800;
			}
			var n = Math.ceil(seconds / 180);
			var i;
			for (i = 1; i < 11; i++) {
			    var timeLeft = (i * 180) - seconds;
				if (timeLeft < 0)  {
					timeLeft = timeLeft + 1800;
				}
				var start = new Date((timeLeft - 180) * 1000).toISOString().substr(14, 5);
				var end = new Date((timeLeft) * 1000).toISOString().substr(14, 5);
				var time = new Date(d.getTime() + (timeLeft - 180) * 1000).toLocaleString('en-GB').substr(12, 5);
				if (n === i) {
					document.getElementById("row"+i).className = "active-row";
					document.getElementById("start"+i).innerHTML = end;
					document.getElementById("time"+i).innerHTML = "Active";
				} else {
					document.getElementById("row"+i).className ="";
					document.getElementById("start"+i).innerHTML = start;
					document.getElementById("time"+i).innerHTML = time;
				}
			}
		}
		function loadOffset() {
			document.getElementById("seconds").value = secondsOffset;
			document.getElementById("minutes").value = minutesOffset;
		}
		function loadData() {
			var row;
			var column;
			for (column = 1; column <= 6; column++) {
				for (row = 1; row <= 10; row++) {
					var id = row +  '-' + column;
					var id = row +  '-' + column;
					document.getElementById(id).innerHTML = '<input type="text" id="mission-' + id + '" name="mission-' + id + '" list="missions">';
					document.getElementById('mission-' + id).value = missions[column - 1][row - 1];
				}
			}
			document.getElementById('edited').innerHTML =  date  + ' ' + editor;
		}
		function resetData() {
			var minutesOffset = document.getElementById("minutes").value;
			var secondsOffset = document.getElementById("seconds").value;
			document.getElementById("save-form").reset(); 
			document.getElementById("minutes").value = minutesOffset;
			document.getElementById("seconds").value = secondsOffset;
		}
		</script>
		<style>
			body {
				font-family: sans-serif;
				font-size: 16px;
			}
			.button {
				min-width: 90px;
			}
			label {
				font-size: 16px;
				font-family: sans-serif;
			}
			.styled-table {
				border-collapse: collapse;
				margin: 5px 0 6px 0;
				font-size: 0.9em;
				border-radius: 5px;
				overflow: hidden;
				box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
			}
			.styled-table label {
				font-size: 0.9em;
			}
			.styled-table thead tr {
				background-color: #333333;
				color: #ffffff;
				text-align: left;
			}
			.styled-table th, .styled-table td {
				padding: 12px 15px;
			}
			.styled-table td:nth-of-type(1),
			.styled-table td:nth-of-type(2) {
				padding: 5px 15px;
			}
			.styled-table tr:last-of-type td:nth-of-type(2) {
				padding: 5px;
			}
			.styled-table td {
				padding: 5px;
			}
			.styled-table tbody td {
				border-right: 1px solid #dddddd;
			}
			.styled-table input[type=text]{
				border: none;
			}
			.styled-table tbody tr:last-of-type td {
				background-color: #333333;
				color: #ffffff;
				text-align: right;
				font-size: 11px;
				border: none;
			}
			.styled-table .editing {
				background-color: #ffffff;
			}
			.styled-table tbody tr:nth-of-type(even),
			.styled-table tbody tr:nth-of-type(even) input {
				background-color: #f3f3f3;
			}
			.styled-table tbody tr td:last-of-type {
				border-right: none;
			}
			.styled-table tbody tr.active-row {
				font-weight: bold;
				background-color: #cccccc;
			}
			.styled-table tbody tr.active-row input {
				background-color: #cccccc;
			}
			.styled-table tbody tr.active-row td {
				border-width: 1px 0px 1px 0px;
				border-style: solid;
				border-color: #ffa600;
				border-right: 1px solid #bbbbbb;
			}
			.styled-table tbody tr.active-row td:last-of-type {
				border-right: none;
			}
			.styled-table tbody tr:last-of-type td:last-of-type {
				text-align: center;
			}
			.fade-in {
				animation: fadeIn ease 1s;
				-webkit-animation: fadeIn ease 1s;
				-moz-animation: fadeIn ease 1s;
				-o-animation: fadeIn ease 1s;
				-ms-animation: fadeIn ease 1s;
			}
			@keyframes fadeIn {
				0% {
					opacity:0;
				}
				100% {
					opacity:1;
				}
			}

			@-moz-keyframes fadeIn {
				0% {
					opacity:0;
				}
				100% {
					opacity:1;
				}
			}

			@-webkit-keyframes fadeIn {
				0% {
					opacity:0;
				}
				100% {
					opacity:1;
				}
			}

			@-o-keyframes fadeIn {
				0% {
					opacity:0;
				}
				100% {
					opacity:1;
				}
			}
			@-ms-keyframes fadeIn {
				0% {
					opacity:0;
				}
				100% {
					opacity:1;
				}
			}
		</style>
</head>
<body onload="loadOffset();startUpdate();loadData();">
	<form id="save-form" name="save-form" method="post">
	<datalist id="missions">
		<option value="Albatross">
		<option value="Anvil">
		<option value="Banshee">
		<option value="Basilisk">
		<option value="Cavalry">
		<option value="Cerberus">
		<option value="Dire Wolf">
		<option value="Erebos">
		<option value="Frostbite">
		<option value="Ghost Hunter">
		<option value="Harbinger">
		<option value="Hydra">
		<option value="Kodiak">
		<option value="Leviathan">
		<option value="Life Jacket">
		<option value="Meltdown">
		<option value="Onyx">
		<option value="Perseus">
		<option value="Phalanx">
		<option value="Prometheus">
		<option value="Quarterback">
		<option value="Raiding Party">
		<option value="Red Opossum">
		<option value="Ricochet">
		<option value="Rolling Thunder">
		<option value="Sapphire">
		<option value="Scorpio">
		<option value="Snake Bite">
		<option value="Spearhead">
		<option value="Starry Night">
		<option value="Stormy Winter">
		<option value="Tiger Claw">
		<option value="Tsunami">
		<option value="Umbrella">
		<option value="Watchdog">
		<option value="Wildfire">
		<option value="Zero Hour">
	</datalist>
	<table id="pve" class="styled-table">
		<thead>
		<tr>
			<th>Start
			<th>Time
			<th>1-3 Standard
			<th>1-2 Hardcore
			<th>4-6 Standard
			<th>3-6 Hardcore
			<th>7-10 Standard
			<th>7-10 Hardcore
		<tbody>
		<tr id="row1">
			<td id="start1">
			<td id="time1">
			<td>
				<span id="1-1"></span>
			<td>
				<span id="1-2"></span>
			<td>
				<span id="1-3"></span>
			<td>
				<span id="1-4"></span>
			<td>
				<span id="1-5"></span>
			<td>
				<span id="1-6"></span>
		<tr id="row2">
			<td id="start2">
			<td id="time2">
			<td>
				<span id="2-1"></span>
			<td>
				<span id="2-2"></span>
			<td>
				<span id="2-3"></span>
			<td>
				<span id="2-4"></span>
			<td>
				<span id="2-5"></span>
			<td>
				<span id="2-6"></span>
		<tr id="row3">
			<td id="start3">
			<td id="time3">
			<td>
				<span id="3-1"></span>
			<td>
				<span id="3-2"></span>
			<td>
				<span id="3-3"></span>
			<td>
				<span id="3-4"></span>
			<td>
				<span id="3-5"></span>
			<td>
				<span id="3-6"></span>
		<tr id="row4">
			<td id="start4">
			<td id="time4">
			<td>
				<span id="4-1"></span>
			<td>
				<span id="4-2"></span>
			<td>
				<span id="4-3"></span>
			<td>
				<span id="4-4"></span>
			<td>
				<span id="4-5"></span>
			<td>
				<span id="4-6"></span>
		<tr id="row5">
			<td id="start5">
			<td id="time5">
			<td>
				<span id="5-1"></span>
			<td>
				<span id="5-2"></span>
			<td>
				<span id="5-3"></span>
			<td>
				<span id="5-4"></span>
			<td>
				<span id="5-5"></span>
			<td>
				<span id="5-6"></span>
		<tr id="row6">
			<td id="start6">
			<td id="time6">
			<td>
				<span id="6-1"></span>
			<td>
				<span id="6-2"></span>
			<td>
				<span id="6-3"></span>
			<td>
				<span id="6-4"></span>
			<td>
				<span id="6-5"></span>
			<td>
				<span id="6-6"></span>
		<tr id="row7">
			<td id="start7">
			<td id="time7">
			<td>
				<span id="7-1"></span>
			<td>
				<span id="7-2"></span>
			<td>
				<span id="7-3"></span>
			<td>
				<span id="7-4"></span>
			<td>
				<span id="7-5"></span>
			<td>
				<span id="7-6"></span>
		<tr id="row8">
			<td id="start8">
			<td id="time8">
			<td>
				<span id="8-1"></span>
			<td>
				<span id="8-2"></span>
			<td>
				<span id="8-3"></span>
			<td>
				<span id="8-4"></span>
			<td>
				<span id="8-5"></span>
			<td>
				<span id="8-6"></span>
		<tr id="row9">
			<td id="start9">
			<td id="time9">
			<td>
				<span id="9-1"></span>
			<td>
				<span id="9-2"></span>
			<td>
				<span id="9-3"></span>
			<td>
				<span id="9-4"></span>
			<td>
				<span id="9-5"></span>
			<td>
				<span id="9-6"></span>
		<tr id="row10">
			<td id="start10">
			<td id="time10">
			<td>
				<span id="10-1"></span>
			<td>
				<span id="10-2"></span>
			<td>
				<span id="10-3"></span>
			<td>
				<span id="10-4"></span>
			<td>
				<span id="10-5"></span>
			<td>
				<span id="10-6"></span>
		<tr>
			<td colspan="2" id="edited">
				
			<td>
				<label for="minutes">Offset Minutes</label> 
			<td>
				<input type="number" id="minutes" name="minutes" min="-60" max="60" value="0" required>
			<td>
				<label for="seconds">Offset Seconds</label>
			<td>
				<input type="number" id="seconds" name="seconds" min="-60" max="60" value="0" required>
			<td colspan="2">
				<input class="button" type="button" value="Clear"  onclick="resetData();">
				<input class="button" type="button" value="Load" onclick="loadData();">
				<input class="button" type="submit" value="Save">
	</table>
	<a href="https://github.com/GoldenGnu/armored-warfare-pve-tracker" target="_blank" rel="noopener noreferrer"><img style="margin-top: 6px; margin-bottom: 4px;"  title="GitHub Project" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAACSVBMVEUiKC4kKi4kKi8iKi4VIh0VHSoZJx8ZISAjKi8jJy4kKi0kKS0lKS4qLSkjKCwjKC4jKS0gKS4AAAAjLCwgJi4mLCUjKi0kKC4kKiwjKC0lKC4jKC8kKSwkKC0YJi0ZKTIAMDkXITEiKC8jKSwjKi4iKC0hKCwjKC8iKSwjKS0jKS0kKS0kKS4jKC0iKSwjKi8hKS0iKS0eJykjKC0jKS0jKC0jKS0eJisiKSwfKCkjKS0jKS0kKS0jKC0fJykjKC0jKC0jKC0jKS0kKS0kKS0jKS4jKS4jKS0jKC0jKS0iKCwjKS0jKC4hKi0iKSwkKSwkKiYiKS4iJy4jKC4iKSwjKC0jKC0kKS4OGSIkKi8lKi4kKS0jKS4jKC0jKS0jKC4iJy4iKC0jKS0kKS0kKSwjKS4jKC0jKS0jKS0jKS4kKS4jKS4gKC0iKS4mKComIy8jKi0kLCwkKS0jKC4hJy4jKC4jKDAkMCskIDEkKS4jKS0kKS0jKS0jKS0kKS4kKiwjKC0jKC0jKS0jKS4fKS4kLC4kKC4kKS4jKC4iKS4jKC0jKS0kKC4jKC4jKS0kKC4kKS4jKC4iKC4jKS4iKC0jKC4jKC0kKC0kKS0jKS0kKS4jKC4eKC0jKS0jKC0jKS0kKC4nKiwVJDMjKC4jKC4jKS4fKC8iKC4dKC8jKC4kKS4kKC0kKS0kKS4jKC4fJy8iKS4hKC8jKC4jKC4jKC0jKSwjKi4jKS4jKC4hKC4kKS0kKC4kKS4jKS4kKC0jKC4jKC0jKS3///9tc+XcAAAAunRSTlMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAIDOaHf/f2kOgMCAguX/P6YCwIKtv7+tgrEYMr119T1z1y4lz73kg4VBgQVEIw9oZMEAQEBBYmg3vgpAwIBAQIDHvLe++MKAQEBAQf76g4CAQEBAQvmSgUCAkH32iYBAQIjoTyFqLcuLaft+z6cKYvHHiX5+JkIwdQ9LwQBDuy2CgIIm/EPDvKYCwICAz+OCQiGPgICuquGAAAAAWJLR0TC/W++1AAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB+UBFBMAGRJNfAAAAAEbSURBVBjTARAB7/4AACYnASgpKissKi0uAi8wAwAxBDIzNLq7NTW7vDQ2NwU4AAY5Or07u7u9vL66PL89PgcACDO9P0BBQkNERUZHSL1JCQBKS7xMCk1OT1BRUgtTu0tUAFW8vVYMV1hZWg1bDlzAvF0AXrtfYA9hYmNkZWYQZ2i+aQBqvmtsEW1uEhJvcBNxQ79yAGq+c3QUdXYVd3h5Fnp7v3IAKru6fBB9fhcYf30ZgL+6aQApvIGCgwx+hIWGGodDwbyIAIm/iovBjI0bHI6PkL88kZIAF5PAlJWWlx0QmJm/msGbCwAenJ2en6Chog+jpLrBpaYfAKcgqKnBqqsdC6ytv66vIbAAIrGyC7O0tSMktre4F2a5JdLvaIQoxhNSAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDIxLTAxLTIwVDE5OjAwOjEzKzAwOjAw/7LjMAAAACV0RVh0ZGF0ZTptb2RpZnkAMjAyMS0wMS0yMFQxOTowMDoxMyswMDowMI7vW4wAAAAASUVORK5CYII=" alt="GitHub"></a> 
	<a href="/" target="_blank" rel="noopener noreferrer"><img style="margin-top: 6px; margin-bottom: 4px;"  title="View Site" src=" data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAPJJREFUOI3N0rsuRFEUBuDPhAoho3KJRC1EQ+IdiFrnQUziNbyGW2J4C4WGTCUzpjJGo2AUszbbzpxMyZ+s7LPXv9Z/1mXz15io8NewgbW4t3CHz3GC02igg0FhHZxgpip5Ew9ZQg+XOMdL5n/EVpm8XQQ1MZ/xc7gqxHcSuYTnjOyjjv0ou4uDEOwVLS2KMvNemyHcLoLhoog9q2G2aOcjznxDgxE+mBQDeR/Rwl5U0Y7venAprovlpHRkuONE3kZCwgJuMv4Vu0U1Dos/9HEd9mbMGhNWcer3tJM94djwsX2j6ilPYR0rkdzCvZ9h/iN8AdoQW7Bw21yNAAAAAElFTkSuQmCC" alt="View"></a> 
	</form>
</body>
</html>