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

$conn->query("CREATE TABLE IF NOT EXISTS 'analogcom' ( `ID` INTEGER PRIMARY KEY AUTOINCREMENT, `time` NUMERIC, `analog_id` INTEGER, `comment` TEXT, `repairid` INTEGER, `repcode` TEXT, `test_name` TEXT, `boardid` TEXT )");

echo $id;
echo $comment;
echo $repid;
$newdate = new DateTime();
$date = $newdate->format('d-m-Y H:i:s') . "\n";
echo $date;


$conn->query("INSERT INTO analogcom (ID, analog_id, time, comment, repcode, test_name, boardid) VALUES (NULL, '$id', '$date', '$comment', '$repid', '$name', '$boardid') ");  
$conn->query("UPDATE analog SET comment='1' WHERE analog_id ='$id'");  
$conn->connection = null;


header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?> 