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
$boardid = $_GET["serial"];
$pre = substr("$boardid", 0,3); 
$db = "$dir\\$pre\\repair_db.sqlite3";
$conn = new SQLite3($db) ;

$id = $_GET["id"];
$comment = $_GET["comment"];
$repid = $_GET["repair"];
$name = $_GET["testname"];

$conn->query("CREATE TABLE IF NOT EXISTS `testjetcom` ( 
	`ID` INTEGER PRIMARY KEY AUTOINCREMENT, 
	`time` NUMERIC, 
	`testjet_id` NUMERIC, 
	`comment` TEXT, 
	`repairid` INTEGER, 
	`repcode` INTEGER, 
	`test_name` TEXT, 
	`boardid` TEXT )");

echo $id;
echo $comment;
echo $repid;
$newdate = new DateTime();
$date = $newdate->format('d-m-Y H:i:s') . "\n";
echo $date;


$conn->query("INSERT INTO testjetcom (ID, testjet_id, time, comment, repcode, test_name, boardid) VALUES (NULL, '$id', '$date', '$comment', '$repid', '$name', '$boardid') ");  
$conn->query("UPDATE testjet_test SET comment='1' WHERE testjet_id ='$id'");  
$conn->connection = null;


header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?> 