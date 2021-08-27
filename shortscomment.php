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
$source = $_GET["source"];
$destination = $_GET["destination"];

$conn->query("CREATE TABLE IF NOT EXISTS 'shortscom' ( `ID` INTEGER PRIMARY KEY AUTOINCREMENT, `time` NUMERIC, `shorts_id` INTEGER, `comment` TEXT, `repairid` INTEGER, `repcode` INTEGER, `test_name` INTEGER, `boardid` INTEGER, `source` INTEGER, `destination` INTEGER )");
echo $id;
echo $comment;
echo $repid;
$newdate = new DateTime();
$date = $newdate->format('d-m-Y H:i:s') . "\n";
//echo $newdate;
echo $date;


$conn->query("INSERT INTO shortscom (ID, shorts_id, time, comment, repcode, test_name, boardid, source, destination) VALUES (NULL, '$id', '$date', '$comment', '$repid', '$name', '$boardid', '$source', '$destination') ");  
$conn->query("UPDATE shorts_test SET comment='1' WHERE shorts_test_id ='$id'");  
$conn->connection = null;


header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?> 