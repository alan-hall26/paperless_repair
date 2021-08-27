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
$conn->query("UPDATE analog SET view='1' WHERE analog_id ='$id'");


$resultanalog =  $conn->query("SELECT * FROM analog WHERE analog_id='$id' ");
if ($resultanalog->num_rows = 1) {
     //output data of each row

while($rowanalog = $resultanalog->fetchArray()) {
	
$boardid =  $conn->query("SELECT serial FROM btest WHERE test_id='$rowanalog[test_id]' ");
$board = $boardid->fetchArray();



echo "<table><tr><td>TESTNAME: " . $rowanalog["test_name"] . "</a></td>
<td>BOARD ID: <a href = '/submit.php?id=" . $board["serial"] . "'>" . $board["serial"] . "</tr>";
if ($rowanalog["subtest"] != ''){
	echo "<TR><TD>SUBTEST: " . $rowanalog["subtest"]. "</tr>";}
echo "<tr><td>Test Type</td><td>Measured Value</td><td>Nominal Value</td><td>Upper Limit</td><td>Lower Limit</td></tr>
<tr><td>" . $rowanalog["test_type"]. "</td><td>" . $rowanalog["measured_val"]. "</td><td>" . $rowanalog["nominal_val"]. "</td><td>" . $rowanalog["upper_limit"]. "</td><td>" . $rowanalog["lower_limit"]. "</td></tr>
</table>
<p>
<table><tr><form action=analogcomment.php>	";

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
  <input type=hidden name=id value='$rowanalog[analog_id]'>
  <input type=hidden name=serial value='$board[serial]'>
   <input type=hidden name=testname value='$rowanalog[test_name]'>
  <input type=submit value=update>
</form></td></tr></table>	<p><P><P><P><P>
";


//comment out

{if ($rowanalog["comment"] > 0 ) {
	
echo "<table><tr><td>Time</td><td>Comment</td><td>Repair Type</td></tr><p><P><P><P><P>";
$comout = $rowanalog["analog_id"];
$resultcomout = $conn->query("SELECT * FROM analogcom WHERE analog_id='$comout'");

while($rowcomout = $resultcomout->fetchArray()) {
	echo "
<tr style=background-color:#FFB347><td>" . $rowcomout['time'] ."</td>
	
	<td style=background-color:#FFB347>" . $rowcomout['comment'] ."</td>
	
	<td  style=background-color:#FFB347>" . $rowcomout['repcode'] ."</td></tr>" ;
}}};
echo "</table>";
if(strpos($rowanalog["test_name"], '%') !== false){
$subname = substr($rowanalog["test_name"], 0, strpos($rowanalog["test_name"], '%'));
echo "<iframe src='/pdfs/"; echo $pre; echo".pdf#search=".$subname."&zoom=600'  width='1000' height='50%' frameborder='0'></iframe>";
}
else
{
echo "<iframe src='/pdfs/"; echo $pre; echo".pdf#search=".$rowanalog["test_name"]."&zoom=600'  width='1000' height='50%' frameborder='0'></iframe>";
}
echo "
<p><P><P><P><P>";


$prevout = $rowanalog["analog_id"];

$resultprevout = $conn->query("SELECT * FROM analogcom WHERE test_name ='$rowanalog[test_name]'");

if ($resultprevout > "0") {
echo"<table><th>Previous Repairs for " . $rowanalog["test_name"]. "</th></table>
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
}else
{echo "no";}
}};


$conn->close();
?>