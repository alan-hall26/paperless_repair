<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type='text/javascript' src="jquery/jquery.min.js"></script>
<script> 
    public SQLite3Result SQLite3::query ( string $query )
</script>
<style>

table {
  border: 0px solid black;
  display: inline-table;
  width: 200;
   background-color:#f6f6f6; color:#132D60;
}
</style>
<?php
include('header.php');
?>

<?php

$board1 = $_GET["id"];
$pre = substr("$board1", 0,3); 
$db = "$dir\\$pre\\repair_db.sqlite3";
if (file_exists($db)) {
$conn = new SQLite3($db) ;

$poscheck = $conn->query("SELECT position FROM btest WHERE serial='$board1' limit 1");
$poscheck1 = $poscheck->fetchArray();
echo "<BR><panel><table>";
if ($poscheck1['position'] != '1')
{
	echo "The Board code entered <a href='submit.php?id=".$board1."'>(".$board1.")</a> is in position ".$poscheck1['position'].", please enter the code from position 1";

echo "	<BR><BR><tr><td>Enter First Board for Panel Information<form  id='myForm' action='panel.php' method='get'> 
    <input autofocus='autofocus' type='text' id='barcode' name='id' /> 
</form></td></tr></table></main>";
}
else
{

$amount = $conn->query("SELECT position FROM btest ORDER BY `position` DESC limit 1");
$amount = $amount->fetchArray();
$testdate = $conn->query("SELECT date FROM btest WHERE serial='$board1' ORDER BY `date` DESC limit 1");
$date=$testdate->fetchArray();

$day = substr($date['date'], 0,2); 	
$month = substr($date['date'], 2,2); 
$year = substr($date['date'], 4,2); 
$hour = substr($date['date'], 6,2); 
$minute = substr($date['date'], 8,2); 
$second = substr($date['date'], 10,2); 




Echo "<br><br> Panel ".$board1.", last tested at ".$year."/".$month."/".$day."-".$hour.":".$minute.":".$second."";


echo "<br><br>
<tr><td>Board Name</td></tr>
<tr><td>Shorts Test</td></tr>
<tr><td>Pins Test</td></tr>
<tr><td>Analog Test</td></tr>
<tr><td>Testjet Test</td></tr>
<tr><td>Digital Test</td></tr></table>";

$num = 0;

$number = substr("$board1", 3,7); 
$number = $number -1;

do{

$number = sprintf('%07d',$number + 1);
$board = substr_replace($number, $pre, 0, 0); 

$boardid = $conn->query("SELECT * FROM btest WHERE serial='$board' ORDER BY `date` DESC limit 1");
$board1string=$boardid->fetchArray();
$sqlshortcheck = $conn->query("SELECT * FROM shorts_test WHERE test_id='$board1string[test_id]'");
$shortcheck = $sqlshortcheck->fetchArray();
echo "<table><tr><td><a href='submit.php?id=".$board."'>".$board."</a></td><tr>";
{if ($shortcheck != '') 
	{echo "<tr><td style=background-color:#FF6961>FAIL</font>";
}
else
	{echo "<tr><td style=background-color:#77dd77>PASS</td>";		}};	
//pins
$sqlpinscheck = $conn->query("SELECT * FROM pins_test WHERE test_id='$board1string[test_id]' ");
$pinscheck = $sqlpinscheck->fetchArray();	
{if ($pinscheck != '') 
	{echo "<tr align='left'><td style=background-color:#FF6961>FAIL</font>";}
else
 {echo "<tr align='left'>
<td style=background-color:#77dd77>PASS</td>";}};	
//analog
$sqlanalcheck = $conn->query("SELECT * FROM analog WHERE test_id='$board1string[test_id]'");
$analcheck = $sqlanalcheck->fetchArray();	
{if ($analcheck != '') 
	{echo "<tr><td style=background-color:#FF6961>FAIL</font>";}
else
	{echo "<tr><td style=background-color:#77dd77>PASS</td>";}};
//testjet
$sqltestjetcheck = $conn->query("SELECT * FROM testjet_test WHERE test_id='$board1string[test_id]' ");		
$testjetcheck = $sqltestjetcheck->fetchArray();	
{if ($testjetcheck != '') 
	{echo "<tr><td style=background-color:#FF6961>FAIL</font>";
}else
	{echo "<tr><td style=background-color:#77dd77>PASS</td>";};}
//digital
$sqldigitalcheck = $conn->query("SELECT * FROM digital_test WHERE test_id='$board1string[test_id]' ");
$digitalcheck = $sqldigitalcheck->fetchArray();	
{if ($digitalcheck != '') 
	{echo "<tr><td style=background-color:#FF6961>FAIL</font>";
}else
	{echo "<tr><td style=background-color:#77dd77>PASS</td></table>";};}
$num = $num +1;

}
while($board1string['position'] < $amount['position']);

}}
echo "</table></panel>"






?>
</body>