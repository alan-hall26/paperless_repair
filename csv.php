<?php


$dir = "Test_History";

// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="demo.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

$count = $_GET["count"];
$day = $_GET["day"];
$month = $_GET["month"];
$year = $_GET["year"];
$pre = $_GET["db"];
$db = "$dir\\$pre\\repair_db.sqlite3";
$conn = new SQLite3($db) ;



// output the column headings
fputcsv($output, array('Reports For',$pre));


//blank
fputcsv($output, array());


//generation details

$timestamp = date("M-d-Y");
fputcsv($output, array('Generated on', $timestamp));

if ($day != '')
	{
$date = substr_replace($month, $year, 0, 0); 
$date = substr_replace($day, $date, 0, 0); 
$date = str_pad( $date, 12, '0', STR_PAD_RIGHT );
$amount = $conn->query("SELECT test_id FROM btest WHERE date > $date ORDER BY `test_id` ASC limit 1");
$amount = $amount->fetchArray();
$amount = $amount['test_id'];


$count= $conn->query("SELECT test_id FROM btest ORDER BY `test_id` DESC limit 1");
$count = $count->fetchArray();
$count = $count['test_id'];
$subcount = $count - $amount;
if($subcount == $count){
$since = $day ."/". $month ."/" .$year ;
fputcsv($output, array('Showing failures for all ',$count  +1,'Tests since: ',$since ));
}
else{
fputcsv($output, array('Showing Failures For', $subcount +1,'out of',$count +1,'Tests since:', $since ));
}

;}

else{
$amount = $conn->query("SELECT test_id FROM btest ORDER BY `test_id` DESC limit 1");
$amount = $amount->fetchArray();
$actamount = $amount['test_id'];
$amount = $amount['test_id'] - $count;

$date = $conn->query("SELECT date FROM btest WHERE test_id = $amount ORDER BY `test_id` DESC limit 1");
$date = $date->fetchArray();

$year = substr($date['date'], 0,2); 	
$month = substr($date['date'], 2,2); 
$day = substr($date['date'], 4,2); 
$hour = substr($date['date'], 6,2); 
$minute = substr($date['date'], 8,2); 
$second = substr($date['date'], 10,2);

if($count > $actamount){
fputcsv($output, array('Showing failures for all', $actamount +1, 'Tests'));
}
else{
echo "Showing Report For The last ".$count." out of  "; echo $actamount +1; echo"  tests since: ".$day."/".$month."/20".$year."";}
}

//fputcsv($output, array(
//fputcsv($output, array(
//fputcsv($output, array(
//fputcsv($output, array(
//fputcsv($output, array(
//fputcsv($output, array(
//fputcsv($output, array(
//fputcsv($output, array(





//analog
fputcsv($output, array('ANALOG REPORTS'));

fputcsv($output, array('Board ID', 'Board Position', 'Component ID', 'Component Type', 'Value Measured', 'Component Value', 'Upper Limit', 'Lower Limit'));

$analog = $conn->query("SELECT test_name, count(test_name) FROM analog WHERE test_id > $amount ORDER BY count(test_name) DESC ");
{while ($row = $analog->fetchArray(sqlite3_assoc)) fputcsv($output, $row);
}

?>