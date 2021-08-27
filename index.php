<!DOCTYPE html>
<html>
<style>
body
{
font-family: Calibri;
} 
html, body {
    width: 100%;
}

main{
    margin: 0 auto;
      text-align:center;
}
table, th, td {
  display: inline-table;
  width: 1080;
}
</style>

<body>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'></script>

<main><table style=background-color:#132D60 >
<tr><td><img width="50%" src=/logo.png></tr></td>
<tr><td style=color:#F9F9F9>Please Scan or Enter Barcode</tr></td>

<tr><td style=color:#F9F9F9>Single Board<form  id="myForm" action="submit.php" method="get"> 
    <input autofocus="autofocus" type="text" id="barcode" name="id" /> 
</form></td></tr>

<tr><td style=color:#F9F9F9>Enter First Board for Panel Information<form  id="myForm" action="panel.php" method="get"> 
    <input autofocus="autofocus" type="text" id="barcode" name="id" /> 
</form></td></tr></table></main>

  <br><br>
<table style=background-color:#f6f6f6><tr><td><a href='graph.php?db=&month=&year='>Reports</td></tr>
		<tr><td>Latest Tested<br>
     <font size="2px"> (Hover over for date)</font></td></tr></table>


<?php

//$dir = "O:\Production\Test_History";
$dir = "Test_History";

// Open a directory, and read its contents
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($file = readdir($dh)) !== false){

$arr = explode(".", $file, 2);
$db = $first = $arr[0];
if ($db !=''){
if ($db !='Software'){
if ($db !='repair_db'){

echo "<table style=background-color:#f6f6f6><tr><td><a href='reports.php?db=".$db."&count=10&day=&month=&year='>".$db."</a></td></tr>";

$dbdir = "Test_History\\$db\\repair_db.sqlite3";


$conn = new sqlite3($dbdir) ;

//$resultboards =  $conn->query("SELECT DISTINCT serial FROM (SELECT serial, date FROM btest WHERE position='1' ORDER BY date DESC ) LIMIT '5'");
$resultboards =  $conn->query("SELECT * FROM btest WHERE position='1' GROUP BY serial ORDER BY date DESC limit '5'");
while ($boards = $resultboards->fetchArray()){

$day = substr($boards['date'], 0,2);  
$month = substr($boards['date'], 2,2); 
$year = substr($boards['date'], 4,2); 
$hour = substr($boards['date'], 6,2); 
$minute = substr($boards['date'], 8,2); 
$second = substr($boards['date'], 10,2); 

echo "<tr><td title='".$year."/".$month."/".$day."-".$hour.":".$minute.":".$second."'>
<a href='panel.php?id=".$boards['serial']."'>".$boards['serial']."</a></td></tr>

";
}
Echo "</table>";
$conn->close();



}}}

    }
    closedir($dh);
  }
}
?>

</body>
</html> 
