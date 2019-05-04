<?php
// set up database connection, and load functions
include('db_connection.php');
include('db_functions.php');

// check whether a borough has been selected
if (isset($_GET['filter_borough'])) {
	$borough = $_GET['filter_borough'];
	$boroughSelected = true;
} else {
	$boroughSelected = false;
}

// check whether a time has been selected
if (isset($_GET['filter_time'])) {
	$startTime = $_GET['filter_time'];
	$timeSelected = true;
} else {
	$timeSelected = false;
}


// check whether "all times" has been selected
if (isset($_GET['filter_all_times'])) {
	$allTimesSelected = true;
} else {
	$allTimesSelected = false;
}

// create "show all times" check box and set to previously selected value 
$timeCheckboxStr = "<br><input type=checkbox name=filter_all_times value=25";
if ($allTimesSelected ) {
	$timeCheckboxStr .= " checked> Show all times<br>";
} else {
	$timeCheckboxStr .= "> Show all times<br>";
}

// create time slider and set to previously selected value
$timeSliderStr = "<input name=filter_time type=range min=0 max=23 value=";
if ($timeSelected && !$allTimesSelected) {
	$timeSliderStr .= $startTime;
} else {
	$timeSliderStr .= 0;
}
$timeSliderStr .= " class=slider id=myRange onchange=updateSliderValue(this.value) onload=updateSliderValue(this.value) list=steplist>
	<datalist id=steplist>
	<option value=0>
	<option value=1>
	<option value=2>
	<option value=3>
	<option value=4>
	<option value=5>
	<option value=6>
	<option value=7>
	<option value=8>
	<option value=9>
	<option value=10>
	<option value=11>
	<option value=12>
	<option value=13>
	<option value=14>
	<option value=15>
	<option value=16>
	<option value=17>
	<option value=18>
	<option value=19>
	<option value=20>
	<option value=21>
	<option value=22>
	<option value=23>
	<option value=24>
	</datalist>";

// create borough selection form and set to previously selected value
$query = "SELECT DISTINCT(borough) FROM lsoa_to_borough ORDER BY borough;";
$results = db_assocArrayAll($dbh,$query);
$val = 1;
$formBoroughStr = "<option value=0>ALL</option>";
$boroughNames = array();

foreach ($results as $row) {
	$formBoroughStr .= "<option value=$val";
	// make sure selected borough continues to be selected
	if ($boroughSelected) {
		if ($val == $borough) {
			$formBoroughStr .= " selected ";
		}
	}
	$formBoroughStr .= ">" . $row['borough'] . "</option>";
	//store in array
	$boroughNames[$val] = $row['borough'];
	$val++; 
}
$boroughMin = 1;
$boroughMax = $val - 1;

// Initiate SQL queries for map data
$query = "SELECT * FROM acc2k";

// Initiate SQL queries for causalty data
$queryCasSeverity = "SELECT casualty_severity, COUNT(*) AS num_casualties FROM acc2k LEFT JOIN cas ON acc2k.accident_index = cas.accident_index";
$queryCasAge = "SELECT age_band_of_casualty, COUNT(*) AS num_casualties FROM acc2k LEFT JOIN cas ON acc2k.accident_index = cas.accident_index";
$queryCasSex = "SELECT sex_of_casualty, COUNT(*) AS num_casualties FROM acc2k LEFT JOIN cas ON acc2k.accident_index = cas.accident_index";

// modify SQL queries according to borough selected
if ($boroughSelected) {	
	if ($borough >= $boroughMin && $borough <= $boroughMax) {
		$query .= " LEFT JOIN lsoa_to_borough ON acc2k.lsoa_of_accident_location = lsoa_to_borough.lsoa ";
		$queryCasSeverity .= " LEFT JOIN lsoa_to_borough ON acc2k.lsoa_of_accident_location = lsoa_to_borough.lsoa ";
		$queryCasAge .= " LEFT JOIN lsoa_to_borough ON acc2k.lsoa_of_accident_location = lsoa_to_borough.lsoa ";
		$queryCasSex .= " LEFT JOIN lsoa_to_borough ON acc2k.lsoa_of_accident_location = lsoa_to_borough.lsoa ";
		$individualBorough = true;
		$query .= " WHERE lsoa_to_borough.borough = '". $boroughNames[$borough] ."'";
		$queryCasSeverity .= " WHERE lsoa_to_borough.borough = '". $boroughNames[$borough] ."'";
		$queryCasAge .= " WHERE lsoa_to_borough.borough = '". $boroughNames[$borough] ."'";
		$queryCasSex .= " WHERE lsoa_to_borough.borough = '". $boroughNames[$borough] ."'";
	}
}

// modify query according to time selected
if ($timeSelected && !$allTimesSelected){
	if ($individualBorough) {
		$query .= " AND ";
		$queryCasSeverity .= " AND ";
		$queryCasAge .= " AND ";
		$queryCasSex .= " AND ";
	} else {
		$query .= " WHERE ";
		$queryCasSeverity .= " WHERE ";
		$queryCasAge .= " WHERE ";
		$queryCasSex .= " WHERE ";
	}
	$endTime = $startTime + 1;
	$query .= " acc2k.acctime >= time '0".$startTime.":00:00' AND acc2k.acctime <= time '0".$endTime.":00:00'";  
	$queryCasSeverity .= " acc2k.acctime >= time '0".$startTime.":00:00' AND acc2k.acctime <= time '0".$endTime.":00:00'";  
	$queryCasAge .= " acc2k.acctime >= time '0".$startTime.":00:00' AND acc2k.acctime <= time '0".$endTime.":00:00'";  
	$queryCasSex .= " acc2k.acctime >= time '0".$startTime.":00:00' AND acc2k.acctime <= time '0".$endTime.":00:00'";  
}

// Finish up queries
$queryCasSeverity .= " GROUP BY casualty_severity ORDER BY casualty_severity;";
$queryCasAge .= " GROUP BY age_band_of_casualty ORDER BY age_band_of_casualty;";
$queryCasSex .= " GROUP BY sex_of_casualty ORDER BY sex_of_casualty;";
$query .= ";";

// for debugging
// echo $query;

// fetch map data from database and capture in PHP array, then convert to JSON variable
$results = db_assocArrayAll($dbh,$query);
echo "<script type='text/javascript'>";
echo "var myData = ".json_encode($results,JSON_NUMERIC_CHECK);
echo "</script>";

// fetch casualty severity data from database and capture in PHP array, then convert to JSON variable
$resultsCasSeverity = db_assocArrayAll($dbh,$queryCasSeverity);
echo "<script type='text/javascript'>";
echo "var myCasSeverityData = ".json_encode($resultsCasSeverity,JSON_NUMERIC_CHECK);
echo "</script>";

// fetch casualty age data from database and capture in PHP array, then convert to JSON variable
$resultsCasAge = db_assocArrayAll($dbh,$queryCasAge);
echo "<script type='text/javascript'>";
echo "var myCasAgeData = ".json_encode($resultsCasAge,JSON_NUMERIC_CHECK);
echo "</script>";

// fetch casualty age data from database and capture in PHP array, then convert to JSON variable
$resultsCasSex = db_assocArrayAll($dbh,$queryCasSex);
echo "<script>";
echo "var myCasSexData = ".json_encode($resultsCasSex,JSON_NUMERIC_CHECK);
echo "</script>";
?>