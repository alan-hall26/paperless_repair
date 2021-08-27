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
list
{

overflow:auto;
}
</style>
<?php
$name = $_GET["id"];
$pre = $_GET["pre"];
$db = "$dir\\$pre\\repair_db.sqlite3";

$conn = new SQLite3($db) ;

$resultpins =  $conn->query("SELECT * FROM pins_test WHERE test_name='$name'  LIMIT '5'");


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

echo "<tr><td>Board Number</td><td>Position</td><td>Total Pins</td><td>Pin List</td><td>Report</td></tr>";


while($rowpins = $resultpins->fetchArray()) {

$serial = $conn->query("SELECT DISTINCT serial, position FROM btest WHERE test_id ='$rowpins[test_id]' ORDER BY `date` DESC  LIMIT '5'");
$boardnum = $serial->fetchArray();

echo "<tr>
<td><a href='submit.php?id=".$boardnum["serial"]."'>".$boardnum["serial"]."</a></td>
<td>".$boardnum["position"]."</td>
<td>" . $rowpins["total_pins"]. "</td>
<td><list>" . $rowpins["pin_list"]. "<list></td><td>" . $rowpins["report"]. "</td></tr>
";
//comment out

}
echo "</table>
<p>";
$prevout = $rowpins["pins_id"];

$resultprevout = $conn->query("SELECT * FROM pinscom WHERE test_name ='$name'");

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