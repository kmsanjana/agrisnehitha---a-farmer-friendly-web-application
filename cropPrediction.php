<style>
section {
	width: 100%;
	float:left;
}
.query-predict, .result-predict {
	width: 50%;
	float:left;
	min-height:350px;
}
form .form-group {
width:100%;
float:left;	
padding:10px 0px;
}
form .inputs {
	width:300px;
	float:left;
	margin:3px 0px;
}
form label.inputs {
	width:100%;
}
table {
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
  height:25px;
  line-height:25px !important;
}
tr:nth-child(1) {
	 background-color: yellow;
	 font-weight:700;
}
tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<?php
include("header.php");
include("dbconnection.php");
?>
		<div id="featured">
			<div class="container">
				<div class="row">
        <section>
		<div class="query-predict">
		<header>
                                    <h2>Prediction Query</h2>
                              </header>
        <form action="#">
  <div class="form-group">
    <label class="inputs" for="state">State</label>
    <select class="inputs state">
	</select>
  </div>
  <div class="form-group">
    <label class="inputs" for="district">District</label>
    <select class="inputs district">
	</select>
  </div>
  <div class="form-group">
    <label class="inputs" for="season">Season</label>
    <select class="inputs season">
	</select>
  </div>
  <div class="form-group">
    <label class="inputs" for="soil">Soil Type</label>
    <select class="inputs soil">
	<option value="soil">Red soil</option>
    <option value="soil">Black soil</option>
	<option value="soil">Marsh soil</option>
	</select>
  </div>
   <div class="form-group">
    <label class="inputs" for="year">Year</label>
    <select class="inputs year">
	</select>
  </div>
  <button type="submit" class="btn btn-search">Search</button>
</form>
     </div> 
<div class="result-predict">
<header>
                                    <h2>Prediction Result</h2>
                              </header>
<table>
</table>
<div>
        </section>

				</div>
			</div>
		</div>
<?php include("footer.php");?>
<script src="js/lodash.js"></script>
<script>
var objArray = [];
var periodData = [];
var stateData = [];
var distData = [];
var seasonData = [];
// CSV to Array format
function CSVToArray(strData, strDelimiter) {
    // Check to see if the delimiter is defined. If not,
    // then default to comma.
    strDelimiter = (strDelimiter || ",");
    // Create a regular expression to parse the CSV values.
    var objPattern = new RegExp((
        // Delimiters.
        "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +
        // Quoted fields.
        "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +
        // Standard fields.
        "([^\"\\" + strDelimiter + "\\r\\n]*))"), "gi");
    // Create an array to hold our data. Give the array
    // a default empty first row.
    var arrData = [[]];
    // Create an array to hold our individual pattern
    // matching groups.
    var arrMatches = null;
    // Keep looping over the regular expression matches
    // until we can no longer find a match.
    while (arrMatches = objPattern.exec(strData)) {
        // Get the delimiter that was found.
        var strMatchedDelimiter = arrMatches[1];
        // Check to see if the given delimiter has a length
        // (is not the start of string) and if it matches
        // field delimiter. If id does not, then we know
        // that this delimiter is a row delimiter.
        if (strMatchedDelimiter.length && (strMatchedDelimiter != strDelimiter)) {
            // Since we have reached a new row of data,
            // add an empty row to our data array.
            arrData.push([]);
        }
        // Now that we have our delimiter out of the way,
        // let's check to see which kind of value we
        // captured (quoted or unquoted).
        if (arrMatches[2]) {
            // We found a quoted value. When we capture
            // this value, unescape any double quotes.
            var strMatchedValue = arrMatches[2].replace(
                new RegExp("\"\"", "g"), "\"");
        } else {
            // We found a non-quoted value.
            var strMatchedValue = arrMatches[3];
        }
        // Now that we have our value string, let's add
        // it to the data array.
        arrData[arrData.length - 1].push(strMatchedValue);
    }
    // Return the parsed data.
    return (arrData);
}

// convertion of CSV to JSON String

function CSV2JSON(csv) {
    var array = CSVToArray(csv);
    var objArray = [];
    for (var i = 1; i < array.length; i++) {
        objArray[i - 1] = {};
        for (var k = 0; k < array[0].length && k < array[i].length; k++) {
            var key = array[0][k];
            objArray[i - 1][key] = array[i][k]
        }
    }

    //  var json = JSON.stringify(objArray);
    //  var str = json.replace(/},/g, "},\r\n");
    //  console.log(str);
    return objArray;
}
function callingCsv() {
    // csv file Location
    let csvUrl = 'AgrcultureDataset.csv';
    // Calling AJAX 
    $.ajax({
        type: "GET",
        url: csvUrl,
        crossDomain: true,
        dataType: "text",
        success: function (data) {
            objArray = CSV2JSON(data);
            console.log(objArray);
			dropDownList(objArray);
        },
        error: function () {
            console.log("Failed!");
        }
    });
}

function dropDownList(objArray) {
    objArray.forEach(fillIamPeriod);
	objArray.forEach(fillStateName);
	objArray.forEach(fillSeason);
    console.log(periodData);
	console.log(stateData);
	console.log(seasonData);
	createYearList(periodData);
	createStateList(stateData);
	createSeasonList(seasonData);
	let selectState =  $('.state').find(":selected").val();
    getStateData(selectState);
}
function fillIamPeriod(item, index) {
    let datMes = item.Crop_Year;
    let preiod = datMes;
    if (preiod != "") {
        if (periodData.indexOf(preiod) === -1) {
            periodData.push(preiod);
        }
    }
}
function fillStateName(item, index) {
    let sName = item.State_Name;
    let state = sName;
    if (state != "") {
        if (stateData.indexOf(state) === -1) {
            stateData.push(state);
        }
    }
}
function fillDistName(item, index) {
    let sName = item.District_Name;
    let dist = sName;
    if (dist != "") {
        if (distData.indexOf(dist) === -1) {
            distData.push(dist);
        }
    }
}
function fillSeason(item, index) {
    let sName = item.Season;
    let dist = sName;
    if (dist != "") {
        if (seasonData.indexOf(dist) === -1) {
            seasonData.push(dist);
        }
    }
}
function filterYearIam(objArrayIam, newObj) {
    iamPeriodFilterData = [];
    let selDate = newObj.Period;
    for (let i = 0; i < objArrayIam.length; i++) {
        let datMes = objArrayIam[i].Dat_Mes;
        let preiod = datMes.substr(0, 4);
        if (preiod != "" && preiod == selDate) {
            iamPeriodFilterData.push(objArrayIam[i]);
        }
    }
    console.log(iamPeriodFilterData);
}
function createStateList(stateData){
	for (let i = 0; i < stateData.length; i++) {
			let numY = stateData[i];
			$('.state').append('<option value="'+ numY +'">'+ numY +'</option>');
	}
}

function createYearList(periodData){
console.log(periodData)
	for (let i = 0; i < periodData.length; i++) {

		if(i != 1){
			let numY = parseInt(periodData[i]);
			$('.year').append('<option value="'+ numY +'">'+ numY +'</option>');
		}
	}
}
function createDistList(distData){
	for (let i = 0; i < distData.length; i++) {
		let numY = distData[i];
			$('.district').append('<option value="'+ numY +'">'+ numY +'</option>');
	}
}
function createSeasonList(seasonData){
	for (let i = 0; i < seasonData.length; i++) {
		let numY = seasonData[i];
		if(i != 6){
			$('.season').append('<option value="'+ numY +'">'+ numY +'</option>');
		}
	}
}
function filterStateList(objArray, selectState) {
    console.log(objArray);
    let filter_state = _.filter(objArray,
        { 'State_Name': selectState }
    );
   return filter_state; 
}
$('.state').on('change', function() {
  let selectState = this.value;
  getStateData(selectState);
});
$('.btn-search').on('click', function(e) {
  e.preventDefault();
   $('.result-predict table').html("");
 // let stateVal =  $('.state').find(":selected").val();
  let distVal =  $('.district').find(":selected").val();
  let seasonVal =  $('.season').find(":selected").val();
  let yearVal =  $('.year').find(":selected").val();
  let genTblData = filterTableList(objArray, distVal, yearVal, seasonVal);
  $('.result-predict table').append('<tr><th>Year</th><th>Crop</th><th>Area</th><th>Production</th></tr>');
  if(genTblData.length > 1){
		for (let i = 0; i < genTblData.length; i++) {
		let tblData = genTblData[i];
			$('.result-predict table').append('<tr><td>'+ tblData.Crop_Year +'</td><td>'+ tblData.Crop +'</td><td>'+ tblData.Area +'</td><td>'+ tblData.Production +'</td></tr>');
	}  
  }else {
    $('.result-predict tbody').append('<tr><td colspan="4" style="text-align: center;">Data Not Found</td></tr>');
  }
});

function getStateData(selectState){
	let getDist = filterStateList(objArray, selectState);
	console.log(getDist);
	$('.district').html("");
	distData = [];
	getDist.forEach(fillDistName);
	console.log(distData);
	createDistList(distData);
}
function filterTableList(objArray, distVal, yearVal, seasonVal) {
    console.log(objArray);
	console.log(distVal);
	console.log(yearVal);
	console.log(seasonVal);
    let filter_data = _.filter(objArray,
        { 'District_Name': distVal, 'Crop_Year': yearVal, 'Season':  seasonVal}
    );
    return filter_data;
}

callingCsv();
setTimeout(function(){
	$('#skel-panels-defaultWrapper').css('height','auto');
},5);
</script>