function initialise() {
	
	// create the map object
	myMap = new L.Map('mapid');

	// create the tile layer with Leaflet Providers extension
	var osm = L.tileLayer.provider('Stamen.TonerLite').addTo(myMap);
	myMap.addLayer(osm);  

	var latitudeSum = 0;
	var longitudeSum = 0;
	counter = 0;

	// iterate through the array and create markers
	for (item in myData) {
		// choose the appropriate image to use as custom marker
		var customMarkerImage = chooseColour(myData[item].accident_severity)
		var customMarker = L.icon({
    			iconUrl: customMarkerImage,
			iconSize: [30, 30]
			});
		
		// add marker, create and attach popup text
		var marker = L.marker([myData[item].latitude,myData[item].longitude],{icon: customMarker},{opacity: 0.2}).addTo(myMap);
		var txt = "";
		txt+= "<h3>Accident Information</h3>";
		txt+= "Date: " + myData[item].accdate;
		txt+= "<br>Time: " + myData[item].acctime;
		txt+= "<br>Number of vehicles: " + myData[item].number_of_vehicles;
		txt+= "<br>Number of casualties: " + myData[item].number_of_casualties;
		txt+= "<br>Roadtype: " + chooseRoadtype(myData[item].road_type);
		txt+= "<br>Light conditions: " + chooseLightConditions(myData[item].light_conditions);
		txt+= "<br>Weather conditions: " + chooseWeatherConditions(myData[item].weather_conditions);
		txt+= "<br>Road surface: " + chooseRoadSurfaceConditions(myData[item].road_surface_conditions);	
		marker.bindPopup(txt);

		// for calculation of average latitude and longitude values 
		latitudeSum = latitudeSum + myData[item].latitude;
		longitudeSum = longitudeSum + myData[item].longitude;
		counter++;
	}

	// calculates the centre of all the markers by taking average latitude and longitude of all markers and set map view at the averages
	if (latitudeSum != 0 && longitudeSum != 0) {
		averageLatitude = latitudeSum / counter;
		averageLongitude = longitudeSum / counter;
	} else {
		averageLatitude = 51.507351;
		averageLongitude = -0.127758;
	}
	myMap.setView(new L.LatLng(averageLatitude , averageLongitude), 11); 

	// create histogram for casualty sex data
	var layout = {displayModeBar: false};
	var sexType = ["Male", "Female"];
	var sexCount = new Array;
	i = 0;
	for (k=1; k<=sexType.length; k++) {
		for (item in myCasSexData) {
			if ((myCasSexData[item].sex_of_casualty) == k) {
				sexCount[i] = myCasSexData[item].num_casualties;
				i++;
			} else 
				sexCount[i] = 0;
		}
	}
	var sexData = [
		{
			histfunc: "sum",
			y: sexCount,
			x: sexType,
			type: "histogram",
			name: "count"
		}
	]
	Plotly.newPlot('sexHist', sexData, layout);

	// create histogram for casualty severity data
	var severityType = ["Fatal", "Serious", "Slight"];
	var severityCount = new Array;
	i = 0;
	for (k=1; k<=severityType .length; k++) {
		for (item in myCasSeverityData) {
			if ((myCasSeverityData[item].casualty_severity) == k) {
				severityCount [i] = myCasSeverityData[item].num_casualties;
				i++;
			} else 
				severityCount[i] = 0;
		}
	}
	var severityData = [
		{
			histfunc: "sum",
			y: severityCount,
			x: severityType ,
			type: "histogram",
			name: "count"
		}
	]
	Plotly.newPlot('severityHist', severityData, layout);

	// create histogram for casualty age data
	var ageType = ["0-5", "6-10", "11-15", "16-20", "21-25", "26-35", "36-45", "46-55", "56-65", "66-75", "Over 75"];
	var ageCount = new Array;
	i = 0;
	for (k=1; k<=ageType.length; k++) {
		for (item in myCasAgeData) {
			if ((myCasAgeData[item].age_band_of_casualty) == k) {
				ageCount[i] = myCasAgeData[item].num_casualties;
				i++;
			} else 
				ageCount[i] = 0;
		}
	}
	var ageData = [
		{
			histfunc: "sum",
			y: ageCount,
			x: ageType,
			type: "histogram",
			name: "count"
		}
	]
	Plotly.newPlot('ageHist', ageData, layout);


}

// choose image for custom marker based on accident severity level
function chooseColour(int){
	switch(int){
		case 1:
		return 'images/fatal.png'
		break;
		case 2:
		return 'images/serious.png'
		break;
		case 3:
		return 'images/slight.png'
		break;
	}
}

// choose roadtype for marker popup based on index
function chooseRoadtype(int) {
	switch(int){
		case 1:
		return 'Roundabout'
		break;
		case 2:
		return 'One way street'
		break;
		case 3:
		return 'Dual carriageway'
		break;
		case 6:
		return 'Single carriageway'
		break;
		case 7:
		return 'Slip road'
		break;
		case 9:
		return 'Unknown roadtype'
		break;
		case 12:
		return 'One way street/slip road'
		break;
		case -1:
		return 'Data missing'
		break;
	}
}

// choose light conditions for marker popup based on index
function chooseLightConditions(int) {
	switch(int){
		case 1:
		return 'Daylight'
		break;
		case 4:
		return 'Darkness (lights lit)'
		break;
		case 5:
		return 'Darkness (light unlit)'
		break;
		case 6:
		return 'Darkness (no lighting)'
		break;
		case 7:
		return 'Darkness (lighting unknown)'
		break;
		case -1:
		return 'Data missing'
		break;
	}
}

// choose weather conditions for marker popup based on index
function chooseWeatherConditions(int) {
	switch(int){
		case 1:
		return 'Fine no high winds'
		break;
		case 2:
		return 'Raining no high winds'
		break;
		case 3:
		return 'Snowing no high winds'
		break;
		case 4:
		return 'Fine + high winds'
		break;
		case 5:
		return 'Raining + high winds'
		break;
		case 6:
		return 'Snowing + high winds'
		break;
		case 7:
		return 'Fog or mist'
		break;
		case 8:
		return 'Other'
		break;
		case 9:
		return 'Unknown'
		break;
		case -1:
		return 'Data missing'
		break;
	}
}

// choose road surface conditions for marker popup based on index
function chooseRoadSurfaceConditions(int){
	switch(int){
		case 1:
		return 'Dry'
		break;
		case 2:
		return 'Wet or damp'
		break;
		case 3:
		return 'Snow'
		break;
		case 4:
		return 'Frost or ice'
		break;
		case 5:
		return 'Flood over 3cm. deep'
		break;
		case 6:
		return 'Oil or diesel'
		break;
		case 7:
		return 'Mud'
		break;
		case 8:
		return 'Data missing'
		break;

	}
}