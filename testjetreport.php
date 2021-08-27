<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type='text/javascript' src="jquery/jquery.min.js"></script>
<?php
include('header.php');

$id = $_GET["id"];


$conn = new SQLite3($db) ;
$conn->query("UPDATE testjet_test SET view='1' WHERE testjet_id ='$id'");


$resulttestjet =  $conn->query("SELECT * FROM testjet_test WHERE testjet_id='$id' ");
if ($resulttestjet->num_rows = 1) {
     //output data of each row

while($rowtestjet = $resulttestjet->fetchArray()) {
	
$boardid =  $conn->query("SELECT serial FROM btest WHERE test_id='$rowtestjet[test_id]' ");
$board = $boardid->fetchArray();

echo "<table><tr><td>TESTNAME: " . $rowtestjet["test_name"] . "</a></td><td>BOARD ID: <a href = '/submit.php?id=" . $board["serial"] . "'>" . $board["serial"] . "</tr>

<tr><td>Status</td><td>Pin List</td><td>Report</td></tr>
<tr><td>" . $rowtestjet["status"]. "</td><td><pre>"; echo nl2br($rowtestjet["pin_list"]); echo "<pre></td><td>" . $rowtestjet["report"]. "</td></tr>
</table>
<p>
<table><tr><form action=testjetcomment.php>	";

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
  <input type=hidden name=id value='$rowtestjet[testjet_id]'>
  <input type=hidden name=serial value='$board[serial]'>
   <input type=hidden name=testname value='$rowtestjet[test_name]'>
  <input type=submit value=update>
</form></td></tr></table>	<p><P><P><P><P>
";


//comment out

{if ($rowtestjet["comment"] > 0 ) {
	
echo "<table><tr><td>Time</td><td>Comment</td><td>Repair Type</td></tr><p><P><P><P><P>";
$comout = $rowtestjet["testjet_id"];
$resultcomout = $conn->query("SELECT * FROM testjetcom WHERE testjet_id='$comout'");

while($rowcomout = $resultcomout->fetchArray()) {
	echo "
<tr style=background-color:#FFB347><td>" . $rowcomout['time'] ."</td>
	
	<td style=background-color:#FFB347>" . $rowcomout['comment'] ."</td>
	
	<td  style=background-color:#FFB347>" . $rowcomout['repcode'] ."</td></tr>" ;
}}};
echo "</table>";
//echo "<iframe src='top.pdf#search=";echo substr($rowtestjet["test_name"], 0, strpos($rowtestjet["test_name"], "%")); echo"&zoom=300'  width='85%' height='50%' frameborder='0'></iframe>";
echo "<iframe src='/pdfs/"; echo $pre; echo".pdf#search=".$rowtestjet["test_name"]."&zoom=300'  width='85%' height='50%' frameborder='0'></iframe>";

echo "
<p><P><P><P><P>";


$prevout = $rowtestjet["testjet_id"];

$resultprevout = $conn->query("SELECT * FROM testjetcom WHERE test_name ='$rowtestjet[test_name]'");

if ($resultprevout > "") {
echo"<table><th>Previous Repairs for " . $rowtestjet["test_name"]. "</th></table>
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