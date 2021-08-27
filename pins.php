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
$conn->query("UPDATE pins_test SET view='1' WHERE pin_test_id ='$id'");


$resultpins =  $conn->query("SELECT * FROM pins_test WHERE pin_test_id='$id' ");
if ($resultpins->num_rows = 1) {
     //output data of each row

while($rowpins = $resultpins->fetchArray()) {
	
$boardid =  $conn->query("SELECT serial FROM btest WHERE test_id='$rowpins[test_id]' ");
$board = $boardid->fetchArray();

echo "<table><tr><td>TESTNAME: " . $rowpins["test_name"] . "</a></td><td>BOARD ID: <a href = '/submit.php?id=" . $board["serial"] . "'>" . $board["serial"] . "</tr>

<tr><td>Total Pins</td><td>Pin List</td><td>Report</td></tr>
<tr><td>" . $rowpins["total_pins"]. "</td><td>" . $rowpins["pin_list"]. "</td><td>" . $rowpins["report"]. "</td></tr>
</table>
<p>
<table><tr><form action=pinscomment.php>	";

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
  <input type=hidden name=id value='$rowpins[pin_test_id]'>
  <input type=hidden name=serial value='$board[serial]'>
   <input type=hidden name=testname value='$rowpins[test_name]'>
  <input type=submit value=update>
</form></td></tr></table>	<p><P><P><P><P>
";


//comment out

{if ($rowpins["comment"] > 0 ) {
	
echo "<table><tr><td>Time</td><td>Comment</td><td>Repair Type</td></tr><p><P><P><P><P>";
$comout = $rowpins["pin_test_id"];
$resultcomout = $conn->query("SELECT * FROM pinscom WHERE pins_test_id='$comout'");

while($rowcomout = $resultcomout->fetchArray()) {
	echo "
<tr style=background-color:#FFB347><td>" . $rowcomout['time'] ."</td>
	
	<td style=background-color:#FFB347>" . $rowcomout['comment'] ."</td>
	
	<td  style=background-color:#FFB347>" . $rowcomout['repcode'] ."</td></tr>" ;
}}};
echo "</table>";
echo "
<p><P><P><P><P>";


$prevout = $rowpins["pin_test_id"];

$resultprevout = $conn->query("SELECT * FROM pinscom WHERE test_name ='$rowpins[test_name]'");

if ($resultprevout > "") {
echo"<table><th>Previous Repairs for " . $rowpins["test_name"]. "</th></table>
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