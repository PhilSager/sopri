<?php

$source_array = array( "ODC" => "Ohio Death Certificate",
				"BC" => "Bertillon Cards, Prisoner Record",
				"SDC" => "Stillborn Death Certificate",
				"CDC" => "Columbus Death Certificate",
				"IS" => "Boys and Girls Industrial School Record"
				);

$dbcon = parse_ini_file('conf/dbcon.ini');

$lastname_criterion = "";
$firstname_criterion = "";
$county_criterion = "";
$sort = "name";
$sort_criteria = "LastName, FirstName, Year";
$search_criteria = array();
$lastname_exists = false;
$firstname_exists = false;
$county_exists = false;
$index_exists = false;
$year1_exists = false;
$year2_exists = false;
$years_exist = false;
$sort_exists = false;
$nothing_to_search = false;
$message = "";
$no_results = false;
$max_per_page = 20;
$max_rows = 1000;
$start = 1;
$end = $start + ($max_per_page - 1);
$prev_start = 1;
$next_start = $start + $max_per_page;
$num_rows = array();
$is_inside = preg_match('/'.$dbcon['this_address'].'/', $_SERVER["REMOTE_ADDR"]) || preg_match('/127\.0\.0\.1/', $_SERVER["SERVER_ADDR"]);

require 'paginator.class.php';

if ( !empty($_POST['lastname']) || !empty($_GET['lastname']) ) {
	$lastname_exists = true;
	$lastname = "";
	$lastname = !empty($_POST['lastname']) ? trim(substr($_POST['lastname'],0,10)) : trim(substr($_GET['lastname'],0,10));
	if (preg_match('/[^a-zA-Z\'\%\_\?\*\,\-\.\/]/', $lastname)) { exit; }
	$lastname .= "%";
	$lastname = preg_replace(array("/\*/","/\?/","/'/","/%%/"), array("%","_","","%"), $lastname);
	$lastname_criterion = "LastName LIKE '" . $lastname . "'";
	array_push($search_criteria, $lastname_criterion);
}

if (!empty($_POST['firstname']) || !empty($_GET['firstname'])) {
	$firstname_exists = true;
	$firstname = !empty($_POST['firstname']) ? trim(substr($_POST['firstname'],0,6)) : trim(substr($_GET['firstname'],0,6));
	if (preg_match('/[^a-zA-Z\'\%\_\?\*\,\-\.\/ ]/', $firstname)) { exit; }
	$firstname .= "%";
	$firstname = preg_replace(array("/\*/","/\?/","/'/","/%%/"), array("%","_","","%"), $firstname);
	$firstname_criterion = "FirstName LIKE '" . $firstname . "'";
	array_push($search_criteria, $firstname_criterion);
}

if (!empty($_POST['county']) || !empty($_GET['county'])) {
	$county_exists = true;
	$county = !empty($_POST['county']) ? substr($_POST['county'],0,10) : substr($_GET['county'],0,10);
	if (preg_match('/[^a-zA-Z]/', $county)) { exit; }
	$county_criterion = "County = '" . $county . "'";
	array_push($search_criteria, $county_criterion);
}

if (!empty($_POST['index']) || !empty($_GET['index'])) {
	$index_exists = true;
	$index = !empty($_POST['index']) ? substr($_POST['index'],0,3) : substr($_GET['index'],0,3);
	if (preg_match('/[^A-Z]/', $index)) { exit; }
	$index_criterion = "Source = '" . $index . "'";
	array_push($search_criteria, $index_criterion);
}

if (!empty($_POST['year1']) || !empty($_GET['year1'])) {
	$year1 = !empty($_POST['year1']) ? trim(substr($_POST['year1'],0,4)) : trim(substr($_GET['year1'],0,4));
	if (preg_match('/[^0-9]/', $year1)) { exit; }
	$year1_exists = true;
}	
if (!empty($_POST['year2']) || !empty($_GET['year2'])) {
	$year2 = !empty($_POST['year2']) ? trim(substr($_POST['year2'],0,4)) : trim(substr($_GET['year2'],0,4));
	if (preg_match('/[^0-9]/', $year2)) { exit; }
	$year2_exists = true;
}
if ($year1_exists && $year2_exists) {
	$years_exist = true;
	$year_criteria = "Year BETWEEN '" . $year1 . "' AND '" . $year2 . "'";
	array_push($search_criteria, $year_criteria);
}

if (!empty($_POST['sort']) || !empty($_GET['sort'])) {
	$sort = !empty($_POST['sort']) ? substr($_POST['sort'],0,6) : substr($_GET['sort'],0,6);
	if (preg_match('/[^a-z]/', $sort)) { exit; }
	$sort_exists = true;
	if ($sort == "name") {
		$sort_criteria = "LastName, FirstName, Year";
	}
	if ($sort == "county") {
		$sort_criteria = "County, LastName, FirstName";
	}
	if ($sort == "date") {
		$sort_criteria = "STR_TO_DATE(Date, '%m/%d/%Y')";
	}
}

/*
if (!empty($_POST['year1']) || !empty($_GET['year1']) && !empty($_POST['year2']) || !empty($_GET['year2'])) {
	$years_exists = true;
	$year1 = !empty($_POST['year1']) ? substr($_POST['year1'],0,4) : substr($_GET['year1'],0,4);
	if (preg_match('/[^0-9]/', $year1)) { exit; }
	$year2 = !empty($_POST['year2']) ? substr($_POST['year2'],0,4) : substr($_GET['year2'],0,4);
	if (preg_match('/[^0-9]/', $year2)) { exit; }
	$year_criteria = "Year BETWEEN " . $year1 . " AND " . $year2;
	array_push($search_criteria, $year_criteria);
}
*/
if (!empty($_POST['start']) || !empty($_GET['start'])) {
	$year2_exists = true;
	$start = !empty($_POST['start']) ? substr($_POST['start'],0,10) : substr($_GET['start'],0,10);
	if (preg_match('/[^\d]/', $start)) { exit; }
} 

if (!$lastname_exists && !$firstname_exists && !$county_exists && !$year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need something to search";
}
if (!$lastname_exists && $firstname_exists && !$county_exists && !$year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a last name or county or complete year span";
}
if (!$lastname_exists && !$firstname_exists && $county_exists && !$year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span or name";
}
if (!$lastname_exists && !$firstname_exists && $county_exists && $year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span or name";
}
if (!$lastname_exists && !$firstname_exists && $county_exists && !$year1_exists && $year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span or name";
}
if (!$lastname_exists && !$firstname_exists && $index_exists && !$year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span or name";
}
if (!$lastname_exists && !$firstname_exists && $index_exists && $year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span or name";
}
if (!$lastname_exists && !$firstname_exists && $index_exists && !$year1_exists && $year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span or name";
}
if (!$lastname_exists && !$firstname_exists && !$county_exists && $year1_exists && !$year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span and a name or county";
}
if (!$lastname_exists && !$firstname_exists && !$county_exists && !$year1_exists && $year2_exists) { 
	$nothing_to_search = true;
	$message = "Need a complete year span and a name or county";
}
if (!$lastname_exists && !$firstname_exists && !$county_exists && $years_exist) { 
	$nothing_to_search = true;
	$message = "Need a name or county";
}

$statement = "SELECT * FROM " . $dbcon['news_db_table'] . " WHERE ";
$pages = new Paginator;
$criteria_total = count($search_criteria);
if (!$nothing_to_search) {
	for ($i = 0; $i < $criteria_total; $i++) {
		if ($i > 0) { $statement .= " AND "; }
	 	$statement .= array_shift($search_criteria);
	}
	
	$count_statement = preg_replace('/\*/','count(*)',$statement);
	try {  
		$db = new PDO('mysql:host='.$dbcon['thishost'].';dbname='.$dbcon['news_db_title'].';charset=utf8', $dbcon['user'], $dbcon['pass']);  
		$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$num_rows[0] = $db->query($count_statement)->fetchColumn();
		if ($num_rows[0] < 1) { $no_results = true; }
		$pages->items_total = $num_rows[0];
		$pages->mid_range = 5;
		$pages->paginate();
		
		$statement .= " ORDER BY " . $sort_criteria . " " . $pages->limit;
		
		$db = new PDO('mysql:host='.$dbcon['thishost'].';dbname='.$dbcon['news_db_title'].';charset=utf8', $dbcon['user'], $dbcon['pass']);
		$results = $db->query($statement);
		$db = null;
		
	} catch(PDOException $e) {  
		print($e->getMessage());
		die();
	}
}

?>

<!doctype html public 
  "-//w3c//dtd html 4.01 transitional//en"
  "http://www.w3.org/tr/1999/rec-html401-19991224/loose.dtd">
<html>
<head>
<title>Search results</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/> 

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/json2/20121008/json2.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jStorage/0.4.4/jstorage.min.js"></script>
<!-- <script type="text/javascript" src="jquery.redirect.min.js"></script> -->
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>

<link type="text/css" rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/jquery.selectboxit/3.6.0/jquery.selectBoxIt.css" />
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.selectboxit/3.6.0/jquery.selectBoxIt.min.js"></script>

<!--<link rel="stylesheet" href="Messi/messi.css" />
<script src="Messi/messi.js"></script>-->

<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.2.12/jquery.jgrowl.min.css" />
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.2.12/jquery.jgrowl.min.js"></script>

<script type="text/javascript" src="formly/formly.js"></script>
<link rel="stylesheet" href="formly/formly.css" type="text/css" />

<style type="text/css">

body {
	font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
}

.heading {
	text-align:center;
	font-size:120%;padding:20px;
}

.list {
	padding: 4px;
	
}
.savelist {
	float:right;
}
#cartContents {
	/*display:inline-block;*/
	border: 1px solid black;
	margin-bottom: 10px;
	padding: 10px;
	font-size:90%;
	/*text-align:center;*/
}
#cartinfo {
	text-align:center;
	padding: 4px;
}

.paging-section {
	text-align:center;
	padding:6px;
	height: 30px;
}
	
.paginate {
	font-family: Arial, Helvetica, sans-serif;
	font-size: .7em;
}

a.paginate {
	border: 1px solid #000080;
	padding: 2px 6px 2px 6px;
	text-decoration: none;
	color: #000080;
}

a.paginate:hover {
	background-color: #000080;
	color: #FFF;
	text-decoration: underline;
}

a.current {
	border: 1px solid #000080;
	font: bold .7em Arial,Helvetica,sans-serif;
	padding: 2px 6px 2px 6px;
	cursor: default;
	background:#000080;
	color: #FFF;
	text-decoration: none;
}

span.inactive {
	border: 1px solid #999;
	font-family: Arial, Helvetica, sans-serif;
	font-size: .7em;
	padding: 2px 6px 2px 6px;
	color: #999;
	cursor: default;
}

a.navlinks {
	text-decoration:none;
}
a.navlinks:hover {
	color: red;
	text-decoration: underline;
}
.sortBlock {
	float:right;
	clear:both;
	font-size:90%;
}

div.jGrowl div.resultsAlerts {
	background-color: #808080;
	width: 200px;
	min-height: 0px;
	border: 1px solid #000;
}

</style>

<script type="text/javascript">	

function closeSelections() {
  $('ol#cart').remove();
  $('#cartContents').hide();
}
// $.jStorage.get(cartIndex[i])
// http://www.ohiohistorystore.com/Ohio-Death-Certificate-Photocopy-P7464.aspx?didx=SAGER_Ada_A._6_10_1915_Marion_1675_34890,SAGER_Absolum_I._2_1_1924_Franklin_4378_3415
function goToStore() {
	var cartIndex = $.jStorage.index();
	if (cartIndex.length > 0) {
		var cartContents = "";
		var storeItems = new Array();
		var selectionParts;
		var jstorValue;
		var sName;
		var sDate;
		var sCounty;
		var sVol;
		var sCert;
		var didxValue;
		var storeUrl;
		for (var i in cartIndex) {
			
			var sourceVal = cartIndex[i].replace(/(.*)\-.*/, "$1");
			jstorValue = $.jStorage.get(cartIndex[i]);
			selectionParts = jstorValue.split(', ');
			// 0 = lastname, 1 = firstname, 2 = date, 3 = county, 4 = identifier (vol. and cert.)
			sName = selectionParts[0] + "_" + selectionParts[1];
			sName = sName.replace(/ /g, "_");
			sDate = selectionParts[2].replace(/\//g, "_");
			//if ($.trim(sDate) == "") { sDate = "n/a"; }
			sCounty = selectionParts[3].replace(/ .*$/, "");
			//if ($.trim(sCounty) == "County") { sCounty = "n/a"; }
			if (/Vol/.test(selectionParts[4])) { // if identifier has a Vol, extract it along with Cert
				sVol = selectionParts[4].replace(/Vol\. ([\d].*?) .*$/, "$1");
				sCert = selectionParts[4].replace(/.*Cert\. ([\dS].*)/, "$1");
			} else if (/^Cert.*/.test(selectionParts[4])) { // if identifier does not have a volume put in placeholder 000 
				sVol = "000";
				sCert = selectionParts[4].substring(6);
			} else {
				sVol = "000";
				sCert = selectionParts[4];
			}
			/*
			if (/Cert/.test(selectionParts[4])) {
				sCert = selectionParts[4].replace(/.*Cert\. ([\dS].*)/, "$1"); // Vol. 2302 Cert. 41493
			} 
			if (/SA#/.test(selectionParts[4])) {
				sCert = selectionParts[4].replace(/.*(SA#[\d].*)/, "$1"); // Vol. 1 SA#1097/p.36/Inmate#65
			}*/
			var sortYear = $.trim(sDate).replace(/.*(\d\d\d\d)/, "$1");
			
			storeItemString = "|" + sortYear + "|" + $.trim(sName) + "_" + $.trim(sDate) + "_" + $.trim(sCounty) + "_" + $.trim(sVol) + "_" + $.trim(sCert) + " (" + sourceVal + ")";
			storeItems.push(storeItemString);	
			
		}
		storeItems.sort();
		didxValue = storeItems.join(",");
		didxValue = didxValue.replace(/\|.*?\|/g, "");
		//storeUrl = 'http://www.ohiohistorystore.com/Ohio-Death-Certificate-Photocopy-P7464.aspx';
		storeUrl = 'http://www.ohiohistorystore.com/Select-Ohio-Public-Record-Photocopy-P8888.aspx';
		//$().redirect('http://www.ohiohistorystore.com/Ohio-Death-Certificate-Photocopy-P7464.aspx', { 'didx': didxValue });
			
        $('<form>', {
		    "id": "selectionsForm",
		    "html": '<input type="hidden" id="didx" name="didx" value="' + didxValue + '" />',
		    "action": storeUrl,
		    "target": "_blank",
		    "method": "POST"
		}).appendTo(document.body).submit();	
        
	}

}

function removeSelection(item) {
	var cartIndex = $.jStorage.index();
	var updatedNumber = cartIndex.length-1
	var itemNum = item.substring(5);
	
	for (var i in cartIndex) {
		if (cartIndex[i] == itemNum) {
			$.jStorage.deleteKey(cartIndex[i]);
		}
	}
	// remove the link with list item
	$('#cartTotal').text(updatedNumber);
	$('#'+item).remove();
	
	if ($('#checkbox-'+itemNum).is(':checked')) {
		$('#checkbox-'+itemNum).prop("checked", false);
	}
	if (updatedNumber < 1) {
		$('#cartContents').hide();
	}
}

$(document).ready(function() {
	
	$("select").selectBoxIt();
	$("select#sortResults").change(function() {
		var pageno = $('a.current').html();
    	var sortparam = $(this).val();
    	var params = $( "form#searchVals" ).serialize();
    	var sortUrl = 'http://' + window.location.host + '/death/results.php?' + params + '&page=' + pageno + '&ipp=25&sort=' + sortparam;
    	location.href = sortUrl;
    }); 
	 
	$('#nameResults').formly(); 
	
	$('#cartContents').hide();
	
	$("input[type='checkbox']").click(function() {
		
		var rowId = $(this).attr('ID');
		var rowIdNum = $(this).attr('ID').substring(9);
		
		//$.jStorage.deleteKey(key)
		//$.jStorage.index()
		//$.jStorage.flush()
		var selections = $.jStorage.index();

		if (selections.length > 0) {
			//var selections = $.jStorage.get('didx');
			//selections = selections + '|' + $(this).next().text();
			var alreadySaved = false;
			for (var i in selections) {
				if (selections[i] == rowIdNum) {
					alreadySaved = true;
					$.jStorage.deleteKey(selections[i]);
					$('#cartTotal').text(selections.length-1);
					$.jGrowl("Removed", { theme: 'resultsAlerts',  live: 1000 });
					$(this).prop('checked', false);
					break;
				}
			}
			
			if (!alreadySaved) { 
				var cartIndex = $.jStorage.index();
				var checkedValue = $.jStorage.set(rowIdNum, $(this).next().text());
				checkedValue = checkedValue.replace(/^.*?(\d.*)$/,"$1");
				checkedValue = checkedValue.replace(/\//g,"");
				
				var possibleDuplicate = false;
				for (var i in cartIndex) {
					var jstoreValue = $.jStorage.get(cartIndex[i]);
					var cartValue = jstoreValue.replace(/^.*?(\d.*)$/,"$1");
					cartValue = cartValue.replace(/\//g,"");
					var re = new RegExp( cartValue, "g" );
					possibleDuplicate = re.test(checkedValue);
					if (possibleDuplicate) { 
						$.jGrowl("Note: This appears to be a duplicate of an entry already in your cart. For more information, please contact death@ohiohistory.org", { live: 10000 });
						break; 
					}
				}
				
				$(this).prop('checked', true);
				$.jStorage.set(rowIdNum, $(this).next().text());
				$.jGrowl("Saved", { theme: 'resultsAlerts',  live: 1000 });
				$('#cartTotal').text(selections.length+1);
			}
		} else {
			$.jStorage.set(rowIdNum, $(this).next().text());
			//console.log($(this).next().text());
			$.jGrowl("Saved", { theme: 'resultsAlerts',  live: 1000 });
			//$.jGrowl(selections.length+1, { theme: 'resultsAlerts',  live: 1000 });
			$('#cartTotal').text(selections.length+1);
		}
		
	});
	
	$('a#navView').click(function() {
    	$('#cartContents').remove();
	    				
    	cartIndex = $.jStorage.index();
		if (cartIndex.length > 0) {
			var cartContents = "";
			for (var i in cartIndex) {
				cartContents = cartContents + '<li id="cart_'+cartIndex[i]+'">' + $.jStorage.get(cartIndex[i]) + ' &nbsp;<a class="cartlink" href="#" onclick="removeSelection(\'cart_' + cartIndex[i] + '\')">Remove</a><br/></li>';
			}
			
			var cartWrapper = 
	    	'<div id="cartContents" style="width:50%;margin: 0 auto;" class="formlyWrapper-Base">' +
				'<input type="button" id="closeThis" href="#" onclick="closeSelections()" value="close" style="font-size: 10px;"><br/>' +
				'<div id="cartContentsBox">' +
					'<input type="button" name="Purchase" value="Purchase Copies" onclick="goToStore()">' +
					'<ol id="cart">' +
					cartContents +
					'</ol>' +
				'</div>' +				
			'</div>';
			$('#cartblock').append(cartWrapper);
			$('#cartContents').show();
		}
	    //}
  	});
	
});	
</script>

	
</head>
<body>

<div style="text-align:center;padding:20px;">
	<a class="navlinks" href="index.php">SOPRI Home</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a class="navlinks" href="http://www.ohiohistory.org/collections--archives/archives-library">Library/Archives Home</a>
</div>
 	
<div class="paging-section">
	<?php echo $pages->display_pages(); ?>
</div>
<div id="cartblock" style="width:100%;">
	<!--<div id="cartContents" style="width:50%;margin: 0 auto;" class="formlyWrapper-Base">
		
		<input type="button" id="closeThis" href="#" onclick="closeSelections()" value="close" style="font-size: 10px;">
		<br/>
		<div id="cartContentsBox">
			<input type="button" name="Purchase" value="Purchase Copies" onclick="goToStore()">
		</div>
			
	</div>-->
</div>
<div id="cartinfo">
	<li style="margin-top:6px;list-style-type: none;display:inline-block">
	<script type="text/javascript" language="javascript">
		var listCount = $.jStorage.index();
		document.write('<span id="cartTotal">'+listCount.length+'</span>'); 
	</script>
	&nbsp;item(s) in cart
	<span id="viewCart" class="close">
	  <a class="navlinks" id="navView" href="#">View Cart</a>
	</span></li>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select id="sortResults" name="sort">
		<option value="">Sort by:</option>
		<option value="name">Name</option>
		<option value="date">Date</option>
		<option value="county">County</option>
	</select>

</div>	
<br/>

<?php
	echo( '<div style="width:100%">' );
	echo( '<form id="nameResults" style="margin: 0 auto; width: 500px">' );
	
	if ($nothing_to_search) {
		echo("<p>".$message."</p>");
	} else if ($no_results) { 
		echo("<p>No results.</p>");
	} else {
		
		while($row = $results->fetch(PDO::FETCH_ASSOC)) {
			/*
			$val = $row['Source'];
			$source_name = $source_array[$val];
			echo( '<li class="ui-li ui-li-static ui-btn-up-c">' );
			echo( '<h3 class="ui-li-heading">' . $row['LastName'] );
			echo( preg_match('/[a-zA-Z]/', $row['FirstName']) ? ', ' . $row['FirstName'] : "" );
			echo( preg_match('/[a-zA-Z]/', $row['MiddleName']) ? ' ' . $row['MiddleName'] : "" );
			echo( '</h3> <input type="checkbox" value="">' );
			echo( '<p style="white-space: normal">' );
			echo( "Source: ". $source_array[$val] . ". " );
			echo( "Date: " . $row['Date'] . ", " );
			echo( $row['County'] . " County" );
			echo( ", " . $row['Identifier'] );
			echo( "</p></li>" );
			*/
			
			$val = $row['Source'];
			$source_name = $source_array[$val];
			//$shortId = preg_replace(array('/Vol\./', '/Cert\./', '/\,/', '/ /'), array('','','-',''), $row['Identifier']);
			//echo( '<tr><td>' );
			//echo( '<input type="checkbox" name="checkbox-name" id="checkbox-'.$row['Source'].'-'.preg_replace('/\//',"-",$row['Date']).'-'.$row['County']."-".$shortId.'" class="custom" />&nbsp;' );
			echo( '<input type="checkbox" name="checkbox-name" id="checkbox-'.$row['Source'].'-'.$row['ID'].'" class="custom" />&nbsp;' );
			echo( '<label for="checkbox-'.$row['ID'].'">'. $row['LastName'] );
			echo( preg_match('/[a-zA-Z]/', $row['FirstName']) ? ', ' . $row['FirstName'] : ", n/a" );
			//echo( preg_match('/[a-zA-Z]/', $row['MiddleName']) ? ' ' . $row['MiddleName'] : "" );
			echo( '<span style="white-space: normal; font-size:80%">' );
			echo( ", " );
			echo( (trim($row['Date']) != "") ? $row['Date'] : "00/00/0000" );
			echo( ", " );
			echo( (trim($row['County']) != "") ? $row['County'] . " County" : "na" );
			echo( ", " . $row['Identifier'] );
			//echo( " (Source: ". $source_array[$val] . ")" );
			echo( "</span>" );
			echo( '</label>&nbsp;<span style="white-space: normal; font-size:80%"> ('.$source_name.')</span>' );
			echo( preg_match('/19[56]\d\d\d\d\d\d\d/',$row['Identifier']) && $is_inside ? '<span style="white-space: normal; font-size:80%"> &nbsp;<a href="http://deathcertificateimages.ohiohistory.org/images/display.html?id='.$row['Identifier'].'" target="_blank">Print</a></span>' : "" );
			echo( '<br/>' );
			//echo( '</td></tr>' );
			
		}
		
	}
	echo( '</form>' );
	echo( '<form id="searchVals" action="results.php" method="post">' );
	echo( '<input type="hidden" name="lastname" value="'.($lastname_exists ? $lastname : "").'">' );
	echo( '<input type="hidden" name="firstname" value="'.($firstname_exists ? $firstname : "").'">' );
	echo( '<input type="hidden" name="year1" value="'.($year1_exists ? $year1 : "").'">' );
	echo( '<input type="hidden" name="year2" value="'.($year2_exists ? $year2 : "").'">' );
	echo( '<input type="hidden" name="county" value="'.($county_exists ? $county : "").'">' );
	echo( '<input type="hidden" name="sort" value="'.($sort_exists ? $sort : "").'">' );
	echo( '<input type="hidden" name="start" value="'.$start.'">' );
	echo( '</form>' );
	echo( '</div>' );	
?>

<div style="text-align:center;padding:6px; height: 30px;margin-top:10px;">
   
    <a href="index.php">SOPRI Home</a>&nbsp;&nbsp;
	<?php echo $pages->display_pages(); ?>
	<!--<a href="help.html">Help</a>-->
   
</div> 
	
  </body>
</html>

