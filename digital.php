<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type='text/javascript' src="jquery/jquery.min.js"></script>

<?php

include('header.php');
?>
<style>
body
{
font-family: Consolas;
} 
</style>
<?php
$id = $_GET["id"];

$pre = $_GET["pre"];
$db = "$dir\\$pre\\repair_db.sqlite3";
$conn = new SQLite3($db) ;
$conn->query("UPDATE digital_test SET view='1' WHERE digital_id ='$id'");


$resultdigital =  $conn->query("SELECT * FROM digital_test WHERE digital_id='$id' ");
if ($resultdigital->num_rows = 1) {
     //output data of each row

while($rowdigital = $resultdigital->fetchArray()) {
	
$boardid =  $conn->query("SELECT serial FROM btest WHERE test_id='$rowdigital[test_id]' ");
$board = $boardid->fetchArray();

echo "<table><tr><td>TESTNAME: " . $rowdigital["test_name"] . "</a></td><td>BOARD ID: <a href = '/submit.php?id=" . $board["serial"] . "'>" . $board["serial"] . "</tr>

<tr><td>Test Designator</td><td>Test Status</td><td>Test Substatus</td><td>Failing Vector</td><td>Pin Count</td><td>Pin List</td><td>Report</td></tr>
<tr><td>" . $rowdigital["test_designator"]. "</td><td>" . $rowdigital["test_status"]. "</td><td>" . $rowdigital["test_substatus"]. "</td><td>" . $rowdigital["failing_vector"]. "</td><td>" . $rowdigital["pin_count"]. "</td><td>" . $rowdigital["pin_list"]. "</td><td>" . $rowdigital["report"]. "</td></tr>
</table>
<p>
<table><tr><form action=digitalcomment.php>	";

//

echo "<td><select name='repair'>";

$resultrep = $conn->query("SELECT * FROM repair_codes ");
while($rowrep = $resultrep->fetchArray()) {
$repid = $rowrep['repair_code_id'];
$val = $rowrep['description'];
echo "<option value='$val'>" . $rowrep['description'] . "</option>";
};

echo "</select></td>";

//repair comment

echo "<td>";		
echo"
  <input type=text style='align:top; white-space: wrap; width: 300px; height: 33px; word-wrap: break-word;' maxlength='255' name=comment value=>
  <input type=hidden name=id value='$rowdigital[digital_id]'>
  <input type=hidden name=serial value='$board[serial]'>
   <input type=hidden name=testname value='$rowdigital[test_name]'>
  <input type=submit value=update>
</form></td></tr></table>	<p><P><P><P><P>
";


//comment out

{if ($rowdigital["comment"] > 0 ) {
	
echo "<table><tr><td>Time</td><td>Comment</td><td>Repair Type</td></tr><p><P><P><P><P>";
$comout = $rowdigital["digital_id"];
$resultcomout = $conn->query("SELECT * FROM digitalcom WHERE digital_test_id='$comout'");

while($rowcomout = $resultcomout->fetchArray()) {
	echo "
<tr style=background-color:#FFB347><td>" . $rowcomout['time'] ."</td>
	
	<td style=background-color:#FFB347>" . $rowcomout['comment'] ."</td>
	
	<td  style=background-color:#FFB347>" . $rowcomout['repcode'] ."</td></tr>" ;
}}};
echo "</table>";
//echo "<iframe src='top.pdf#search=";echo substr($rowdigital["test_name"], 0, strpos($rowdigital["test_name"], "%")); echo"&zoom=300'  width='85%' height='50%' frameborder='0'></iframe>";
echo "<iframe src='/pdfs/"; echo $pre; echo".pdf#search=".$rowdigital["test_name"]."&zoom=300'  width='85%' height='50%' frameborder='0'></iframe>";

echo "
<p><P><P><P><P>";


$prevout = $rowdigital["digital_id"];

$resultprevout = $conn->query("SELECT * FROM digitalcom WHERE test_name ='$rowdigital[test_name]'");

if ($resultprevout > "") {
echo"<table><th>Previous Repairs for " . $rowdigital["test_name"]. "</th></table>
	<table><tr><td>Time</td><td>Board Code</td><td>Comment</td><td>Repair Type</td></tr>
";
	while($rowprevout = $resultprevout->fetchArray()) {	



echo"


	<tr style=background-color:#FFB347><td>" . $rowprevout['time'] ."</td>
	<td>" . $rowprevout['boardid'] ."</td>
	
	<td style=background-color:#FFB347>" . $rowprevout['comment'] ."</td>
	
	<td  style=background-color:#FFB347>" . $rowprevout['repcode'] ."</td></tr>" ;
};
echo "</table>";
}
}};


$conn->close();
?>