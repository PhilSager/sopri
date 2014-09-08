<!doctype html public 
  "-//w3c//dtd html 4.01 transitional//en"
  "http://www.w3.org/tr/1999/rec-html401-19991224/loose.dtd">
<html> 
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>  
<title>Name Search</title>

<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<!--<link rel="stylesheet" href="Messi/messi.css" />
<script src="Messi/messi.js"></script>-->

<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.2.12/jquery.jgrowl.min.css" />
<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-jgrowl/1.2.12/jquery.jgrowl.min.js"></script>

<script type="text/javascript" src="formly/formly.js"></script>
<link rel="stylesheet" href="formly/formly.css" type="text/css" />

<script type="text/javascript">
	
	$(document).ready(function() {		
		$('#nameform').formly(); 
	});
	
	function validateForm() {
			
		var year1Exists = /^\d\d\d\d$/.test(document.forms["namesearch"]["year1"].value);
		var year2Exists = /^\d\d\d\d$/.test(document.forms["namesearch"]["year2"].value);
		var lastnameExists = /[A-Na-n]/.test(document.forms["namesearch"]["lastname"].value);
		var firstnameExists = /[A-Na-n]/.test(document.forms["namesearch"]["firstname"].value);
		var countyExists = document.forms["namesearch"]["county"].value != "";
		
		if (!lastnameExists && !firstnameExists && !countyExists && !year1Exists && !year2Exists) {
			//new Messi('Need something to search', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Need something to search", { theme: 'validation', header: 'Search Error', live: 10000 });
			return false;
		}
		if (!lastnameExists && firstnameExists && !countyExists && !year1Exists && !year2Exists) {
			//new Messi('Also need a last name OR a county OR a complete year span', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a last name OR a county OR a complete year span", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
		if (!lastnameExists && !firstnameExists && countyExists && !year1Exists && !year2Exists) {
			//new Messi('Also need a complete year span OR a name', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a complete year span OR a name", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
		if (!lastnameExists && !firstnameExists && countyExists && year1Exists && !year2Exists) {
			//new Messi('Also need a complete year span OR a name', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a complete year span OR a name", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
		if (!lastnameExists && !firstnameExists && countyExists && !year1Exists && year2Exists) {
			//new Messi('Also need a complete year span OR a name', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a complete year span OR a name", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
		if (!lastnameExists && !firstnameExists && !countyExists && year1Exists && !year2Exists) {
			//new Messi('Also need a complete year span and a name OR a county', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a complete year span and a name OR a county", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
		if (!lastnameExists && !firstnameExists && !countyExists && !year1Exists && year2Exists) {
			//new Messi('Also need a complete year span and a name OR a county', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a complete year span and a name OR a county", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
		if (!lastnameExists && !firstnameExists && !countyExists && year1Exists && year2Exists) {
			//new Messi('Also need a name OR a county', {title: 'Search Error', titleClass: 'info', buttons: [{id: 0, label: 'Close', val: 'X'}]});
			$.jGrowl("Also need a name OR a county", { theme: 'validation', header: 'Search Error',  live: 10000 });
			return false;
		}
	}
</script>
<style type="text/css">
body {
	font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
}
.heading {
	text-align:center;
	font-size:120%;padding:20px;
}
.backto {
	text-align:center;
	padding:10px;
}
a.navlinks {
	text-decoration: none;
}
a.navlinks:hover {
	color: red;
	text-decoration: underline;
}
div.jGrowl div.validation {
	background-color: #808080;
	width: 200px;
	min-height: 0px;
	border: 1px solid #000;
}

</style>
</head> 
<body>
	

	
	<div class="backto"><a class="navlinks" href="http://www.ohiohistory.org/collections--archives/archives-library">Library/Archives Home</a></div>
	<div class="heading">Select Ohio Public Records Index</div>
	
	<div style="width:100%">
	
	<form id="nameform" name="namesearch" action="results.php" method="POST" onsubmit="return validateForm()"  style="width: 500px;margin: 0 auto">
	Last name: <input type="search" name="lastname"> First Name: <input type="search" name="firstname"  />
	<br/>County: 
	<select type="search" name="county">
		<option value="" selected>ALL COUNTIES
		<option value="ADAMS">ADAMS
		<option value="ALLEN">ALLEN
		<option value="ASHLAND">ASHLAND
		<option value="ASHTABULA">ASHTABULA
		<option value="ATHENS">ATHENS
		<option value="AUGLAIZE">AUGLAIZE
		<option value="BELMONT">BELMONT
		<option value="BROWN">BROWN
		<option value="BUTLER">BUTLER
		<option value="CARROLL">CARROLL
		<option value="CHAMPAIGN">CHAMPAIGN
		<option value="CLARK">CLARK
		<option value="CLERMONT">CLERMONT
		<option value="CLINTON">CLINTON
		<option value="COLUMBIANA">COLUMBIANA
		<option value="COSHOCTON">COSHOCTON
		<option value="CRAWFORD">CRAWFORD
		<option value="CUYAHOGA">CUYAHOGA
		<option value="DARKE">DARKE
		<option value="DEFIANCE">DEFIANCE
		<option value="DELAWARE">DELAWARE
		<option value="ERIE">ERIE
		<option value="FAIRFIELD">FAIRFIELD
		<option value="FAYETTE">FAYETTE
		<option value="FRANKLIN">FRANKLIN
		<option value="FULTON">FULTON
		<option value="GALLIA">GALLIA
		<option value="GEAUGA">GEAUGA
		<option value="GREENE">GREENE
		<option value="GUERNSEY">GUERNSEY
		<option value="HAMILTON">HAMILTON
		<option value="HANCOCK">HANCOCK
		<option value="HARDIN">HARDIN
		<option value="HARRISON">HARRISON
		<option value="HENRY">HENRY
		<option value="HIGHLAND">HIGHLAND
		<option value="HOCKING">HOCKING
		<option value="HOLMES">HOLMES
		<option value="HURON">HURON
		<option value="JACKSON">JACKSON
		<option value="JEFFERSON">JEFFERSON
		<option value="KNOX">KNOX
		<option value="LAKE">LAKE
		<option value="LAWRENCE">LAWRENCE
		<option value="LICKING">LICKING
		<option value="LOGAN">LOGAN
		<option value="LORAIN">LORAIN
		<option value="LUCAS">LUCAS
		<option value="MADISON">MADISON
		<option value="MAHONING">MAHONING
		<option value="MARION">MARION
		<option value="MEDINA">MEDINA
		<option value="MEIGS">MEIGS
		<option value="MERCER">MERCER
		<option value="MIAMI">MIAMI
		<option value="MONROE">MONROE
		<option value="MONTGOMERY">MONTGOMERY
		<option value="MORGAN">MORGAN
		<option value="MORROW">MORROW
		<option value="MUSKINGUM">MUSKINGUM
		<option value="NOBLE">NOBLE
		<option value="OTTAWA">OTTAWA
		<option value="PAULDING">PAULDING
		<option value="PERRY">PERRY
		<option value="PICKAWAY">PICKAWAY
		<option value="PIKE">PIKE
		<option value="PORTAGE">PORTAGE
		<option value="PREBLE">PREBLE
		<option value="PUTNAM">PUTNAM
		<option value="RICHLAND">RICHLAND
		<option value="ROSS">ROSS
		<option value="SANDUSKY">SANDUSKY
		<option value="SCIOTO">SCIOTO
		<option value="SENECA">SENECA
		<option value="SHELBY">SHELBY
		<option value="STARK">STARK
		<option value="SUMMIT">SUMMIT
		<option value="TRUMBULL">TRUMBULL
		<option value="TUSCARAWAS">TUSCARAWAS
		<option value="UNION">UNION
		<option value="VAN WERT">VAN WERT
		<option value="VINTON">VINTON
		<option value="WARREN">WARREN
		<option value="WASHINGTON">WASHINGTON
		<option value="WAYNE">WAYNE
		<option value="WILLIAMS">WILLIAMS
		<option value="WOOD">WOOD
		<option value="WYANDOT">WYANDOT
	</select>
	<br/>Restrict to years between: <input type="search" name="year1" size="4" maxsize="4"> and <input type="search" name="year2" size="4" maxsize="4" />
	<br/><input type="submit" value="Search" />
</form>

<div style="width:500px;margin: 0 auto;">
	
<p>The Select Ohio Public Records Index includes the following:</p>	

<ul>
	<li>Ohio Department of Health Death Certificates, 1913-1944</li>
	<li>Ohio Department of Health Stillborn Death Certificates, 1913-1935, 1942-1946</li>
	<li>Columbus Board of Health Death Certificates, 1904-1908</li>
	<li>Ohio Girls Industrial School, 1869-1943</li>
	<li>Ohio Boys Industrial School, 1858-1944</li>
</ul>

<p>You can purchase photocopies of Ohio death certificates online. Use the Index to find and select your certificates, save them to your list and click on purchase to checkout through the Ohio History Store. Copies cost $7 per certificate (Ohio residents pay 7% sales tax).</p> 

<p>You can <a href="request.php">request copies</a> of death certificates for the years December 20, 1908 to 1912 through 1953 that don't show up in our online Select Ohio Public Records index by completing an online request form and checking out through the Ohio History Store. Stillborn death certificates are only available from December 20, 1908 to 1935 and 1942-1946.</p>

<p><a href="http://www.ohiohistory.org/collections--archives/archives-library/copy-requests" target="_blank">More information</a> about the Select Ohio Public Records Index.</p>

</div>

</div>


</body>
</html>