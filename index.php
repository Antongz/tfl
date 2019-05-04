<!DOCTYPE html>
<html lang="en">
<head>
	<title>Traffic Accidents London 2017</title>
	<meta charset="UTF-8">
	<style>
		#mapid { height: 440px; margin: 1px;}
	</style>

	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/general.css">
	<link rel="stylesheet" href="css/normalize.css">
	<link href="https://fonts.googleapis.com/css?family=Montserrat%7CNoticia+Text"
                    rel="stylesheet"/>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin=""/>

	<!-- Own JS scripts -->
	<script src="js/project-maplogic.js"></script>

	<!-- Scripts from external packages -->
	<script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
   integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
   crossorigin=""></script>
	<script src="http://unpkg.com/leaflet@1.3.1/dist/leaflet.js"></script>
	<script src="js/leaflet-providers.js"></script>
	<script src="js/plotly-latest.min.js"></script>

	<!-- Include PHP code -->
	<?php include("./project-mapdata.php"); ?>
</head>

<body onload=initialise()>	
	<h1>Traffic Accidents in London in 2017</h1>
	<div id="top">
		<!-- Filters div -->
		<div id="filters">
			<p>This map shows a sample of London road accidents that happened in 2017. Toggle the filters below to display accidents in a particular borough or at a specific time of day, and click the markers to find out more.</p>
			<?php  
			echo "<form action='".$_SERVER['SCRIPT_NAME']."' method='get'>";
			?>
				<h4>Select a borough</h4>	
				<select name=filter_borough>
					<?php echo $formBoroughStr; ?>		
				</select>
				<br><br><br>
				<h4>Select a time</h4>
				<div class="slidecontainer">
					<?php echo $timeSliderStr; ?>
				<p style="display:inline"><span id="timeValue"></span></p>
				</div>	
				<?php echo $timeCheckboxStr; ?>
				<br><br>
				<input type=submit value='Submit'>
			</form>
		</div>
		<!-- Map div -->
		<div id="map-content">
			<div id="mapid"></div>
		</div>
	</div>
	<div id="bottom">
		<!-- Causualty data div -->
		<div id="casualty-data">
			<div id="severity">
				<h4>Casualty Severity Distribution</h4>
				<div id="severityHist" style="width:400px;height:250px;"></div>
			</div>
			<div id="sex">
				<h4>Casualty Gender Distribution</h4>
				<div id="sexHist" style="width:400px;height:250px;"></div>
			</div>
			<div id="age">
				<h4>Casualty Age Distribution</h4>
				<div id="ageHist" style="width:400px;height:250px;"></div>
			</div>
		</div>
		<!-- Map legend div -->
		<div id="legend">
			<p>Map Legend</p>
			<img src="images/fatal.png" alt="Fatal icon" width="25" height="25"><p style="display:inline"> Fatal</p><br>
			<img src="images/serious.png" alt="Fatal icon" width="25" height="25"><p style="display:inline"> Serious</p><br>
			<img src="images/slight.png" alt="Fatal icon" width="25" height="25"><p style="display:inline"> Slight</p>
		</div>
	</div>
	<!-- Citations div -->
	<div id="citations">
		<h5>Data Sources</h5>
		<p>Department for Transport. <cite>Road Safety Data.</cite> Data.gov.uk, 08 November 2018. Web. 28 April 2019. <a href="https://data.gov.uk/dataset/cb7ae6f0-4be6-4935-9277-47e5ce24a11f/road-safety-data">https://data.gov.uk/dataset/cb7ae6f0-4be6-4935-9277-47e5ce24a11f/road-safety-data.</a></p>
		<p>Office for National Statistics. <cite>Super Output Area Population (LSOA, MSOA), London.</cite> London Data Store, 2014. Web. 28 April 2019. <a href="https://data.london.gov.uk/dataset/super-output-area-population-lsoa-msoa-london">https://data.london.gov.uk/dataset/super-output-area-population-lsoa-msoa-london.</a></p>
	</div>
	
	<!-- Script to show range slider value -->
	<script>
		var slider = document.getElementById("myRange");
		var output = document.getElementById("timeValue");
		slider.oninput = function() {
			output.innerHTML = String(this.value) + ":00:00";
		}
	</script>
	</body>
</html>
