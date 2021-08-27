
<?php
include('header.php');
?>

<style>
table, th, td {
  border: 0px solid black;
 width:25%;
}
</style>
<?php

$month = $_GET["month"];
$year = $_GET["year"];
$pre = $_GET["db"];
$data = "$dir\\$pre\\repair_db.sqlite3";
$conn = new SQLite3($data) ;




if (empty($pre)){
echo "please select board code";
}
else {
if (empty($month)){
echo "please select date";
}
else {

$date = substr_replace($month, $year, 0, 0); 


//$amount = $conn->query("SELECT count(*) AS count FROM btest WHERE status=0");
$amount = $conn->query("SELECT count(*) AS count FROM btest WHERE SUBSTR(date, 0, 5) = '$date' AND status=0 ");
$amount = $amount->fetchArray();
$amount = $amount['count'];


$count= $conn->query("SELECT count(*) AS count FROM btest WHERE SUBSTR(date, 0, 5) = '$date'");
$count = $count->fetchArray();
$count = $count['count'];
$subcount = $count - $amount;

if (empty($count)){

    echo "No reports found for ".$pre.", During ".$month."/".$year."<br>";

}
else {


    echo "Report for ".$pre.", ".$month."/".$year."<br><br>";

//echo "Date".$date."<br>";


echo "Full Amount - ".$count."<br>";

echo "Passed - ".$amount."<br>";



echo "Failed - ".$subcount."<br><br><br>";


$pct = 100/$count;


$true = $amount * $pct;
$false = $subcount * $pct;


//echo $true ;
//echo $false;
echo '<table>
<tr><td><div style="background-color:green;color:white;height:10px;width:'.$true.'%;"></div></td><td>'.$true.'%</td></tr>
<tr><td><div style="background-color:red;color:white;height:10px;width:'.$false.'%;"></div></td><td>'.$false.'%</td></tr>
</table>'



;};};};


echo "<table><tr><form action=graph.php>

<td>Select Board Code to see report:<br><select name='db'>";
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

//while (!empty($db)) {
echo "<option value=".$db.">".$db."</option>";
//}


};};};};};};

echo "  
</select>
<select name=month>
<option value='01'>January</option>
<option value='02'>February</option>
<option value='03'>March</option>
<option value='04'>April</option>
<option value='05'>May</option>
<option value='06'>June</option>
<option value='07'>July</option>
<option value='08'>August</option>
<option value='09'>September</option>
<option value='10'>October</option>
<option value='11'>November</option>
<option value='12'>December</option>
</select>
<select name=year>
<option value='16'>2016</option>
<option value='17'>2017</option>
<option value='18'>2018</option>
<option value='19'>2019</option>
</select>
  <input type=submit value=update>

</form></td></tr></table>   <p><P><P><P><P>
";
















echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
end:
?>