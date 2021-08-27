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

$conn->query("UPDATE shorts_test SET view='1' WHERE shorts_test_id ='$id'");


$resultshorts =  $conn->query("SELECT * FROM shorts_test WHERE shorts_test_id='$id' ");
if ($resultshorts->num_rows = 1) {
//output data of each row

while($rowshorts = $resultshorts->fetchArray()) {
	
$boardid =  $conn->query("SELECT serial FROM btest WHERE test_id='$rowshorts[test_id]' ");
$board = $boardid->fetchArray();
$opensget =  $conn->query("SELECT * FROM open_nodes WHERE shorts_test_id='$rowshorts[shorts_test_id]' ");
$opens = $opensget->fetchArray();

echo "<table><tr><td>TESTNAME: " . $opens["source"]. " to " . $opens["destination"]. "</a></td><td>BOARD ID: <a href = '/submit.php?id=" . $board["serial"] . "'>" . $board["serial"] . "</tr>";

$opensget =  $conn->query("SELECT * FROM open_nodes WHERE shorts_test_id='$rowshorts[shorts_test_id]' ");
$opens = $opensget->fetchArray();

echo "<tr><td>Test Report</td><td>Open source</td><td>Destination</td><td>Deviation</td></tr>
<tr><td><pre>"; echo nl2br($rowshorts["report"]); echo "<pre></td><td>" . $opens["source"]. "</td><td>" . $opens["destination"]. "</td><td>" . $opens["deviation"]. "</td></tr>
</table>
<p>
<table><tr><form action=shortscomment.php>	";

//repair list

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
  <input type=hidden name=id value='$rowshorts[shorts_test_id]'>
  <input type=hidden name=serial value='$board[serial]'>
   <input type=hidden name=testname value='$rowshorts[test_name]'>
<input type=hidden name=source value='$opens[source]'>
<input type=hidden name=destination value='$opens[destination]'>
  <input type=submit value=update>
</form></td></tr></table>	<p><P><P><P><P>
";


//comment out

{if ($rowshorts["comment"] > 0 ) {
	
echo "<table><tr><td>Time</td><td>Comment</td><td>Repair Type</td></tr><p><P><P><P><P>";
$comout = $rowshorts["shorts_test_id"];
$resultcomout = $conn->query("SELECT * FROM shortscom WHERE shorts_id='$comout'");

while($rowcomout = $resultcomout->fetchArray()) {
	echo "
<tr style=background-color:#FFB347><td>" . $rowcomout['time'] ."</td>
	
	<td style=background-color:#FFB347>" . $rowcomout['comment'] ."</td>
	
	<td  style=background-color:#FFB347>" . $rowcomout['repcode'] ."</td></tr>" ;
}}};
echo "</table>";
//echo "<iframe src='top.pdf#search=" . $rowshorts["test_name"]. "&zoom=300'  width='100%' height='500' frameborder='0'></iframe>";
echo "
<p><P><P><P><P>";


$prevout = $rowshorts["shorts_test_id"];

$resultprevout = $conn->query("SELECT * FROM shortscom WHERE source ='$opens[source]' AND destination ='$opens[destination]' OR destination ='$opens[destination]' AND  source ='$opens[source]'");
if ($resultprevout > "") {
echo"<table><th>Previous Repairs for Opens betweens " . $opens["source"]. " and " . $opens["destination"]. "</th></table>
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