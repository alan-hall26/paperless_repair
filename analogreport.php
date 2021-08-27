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
$name = $_GET["id"];

$pre = $_GET["pre"];
$db = "$dir\\$pre\\repair_db.sqlite3";

$conn = new SQLite3($db) ;

$resultanalog =  $conn->query("SELECT * FROM analog WHERE test_name='$name'  LIMIT '50'");


if(strpos($name, '%') !== false){
$subname = substr($name, 0, strpos($name, '%'));
echo "<iframe src='/pdfs/"; echo $pre; echo".pdf#search=".$subname."&zoom=300'  width='85%' height='50%' frameborder='0'></iframe>";
}
else
{
echo "<iframe src='/pdfs/"; echo $pre; echo".pdf#search=".$name."&zoom=300'  width='85%' height='50%' frameborder='0'></iframe>";
}
echo "
<p><P><P><P><P>";

echo "<table><tr><td>Component Name: ".$name."</a></td></tr>";

echo "<tr><td>Board Number</td><td>Position</td><td>Test Type</td><td>Measured Value</td><td>Nominal Value</td><td>Upper Limit</td><td>Lower Limit</td></tr>";


while($rowanalog = $resultanalog->fetchArray()) {

$serial = $conn->query("SELECT DISTINCT serial, position FROM btest WHERE test_id ='$rowanalog[test_id]' ORDER BY `date` DESC  LIMIT '5'");
$boardnum = $serial->fetchArray();

echo "<tr>
<td><a href='submit.php?id=".$boardnum["serial"]."'>
".$boardnum["serial"]."
</a></td><td>".$boardnum["position"]."</td><td>" . $rowanalog["test_type"]. "</td><td>" . $rowanalog["measured_val"]. "</td><td>" . $rowanalog["nominal_val"]. "</td><td>" . $rowanalog["upper_limit"]. "</td><td>" . $rowanalog["lower_limit"]. "</td></tr>
";
//comment out

}
echo "</table>
<p>";
$prevout = $rowanalog["analog_id"];

$resultprevout = $conn->query("SELECT * FROM analogcom WHERE test_name ='$name'");

if ($resultprevout > "") {
echo"<table><th>" .$name. " Repair History</th></table>
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

};
$conn->close();
?>