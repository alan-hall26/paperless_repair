
<?php
include('header.php');
?>

<style>
table, th, td {
  border: 0px solid black;
word-wrap:break-word;
    display: inline-table;

   background-color:#f6f6f6; color:#132D60;
}
</style>
<?php

$count = $_GET["count"];
$day = $_GET["day"];
$month = $_GET["month"];
$year = $_GET["year"];
$pre = $_GET["db"];
$db = "$dir\\$pre\\repair_db.sqlite3";
$conn = new SQLite3($db) ;


$amount = $conn->query("SELECT position FROM btest ORDER BY `position` DESC limit 1");
$amount = $amount->fetchArray();
$amount = $amount['position'];

$fetch1 = $amount;
$fetch5 = $amount*5;
$fetch10 = $amount*10;
$fetch20 = $amount*20;
$fetch50 = $amount*50;
$fetch100 = $amount*100;

echo "<br><br><table><tr><form action=reports.php>

<td>Change Range to see latest:<br><select name='count'>
<option value=".$fetch1.">".$fetch1."</option>
<option value=".$fetch5.">".$fetch5."</option>
<option value=".$fetch10.">".$fetch10."</option>
<option value=".$fetch20.">".$fetch20."</option>
<option value=".$fetch50.">".$fetch50."</option>
<option value=".$fetch100.">".$fetch100."</option>
  <input type=submit value=update>
      <input type=hidden name=day value=''>
      <input type=hidden name=month value=''>
      <input type=hidden name=year value=''>
    <input type=hidden name=db value='$pre'>
</form></td></tr></table><p>


<table><tr><td><form action=reports.php>
Or Change To View Tests Since:<br>
<select name=day>
<option value='01'>01</option>
<option value='02'>02</option>
<option value='03'>03</option>
<option value='04'>04</option>
<option value='05'>05</option>
<option value='06'>06</option>
<option value='07'>07</option>
<option value='08'>08</option>
<option value='09'>09</option>
<option value='10'>10</option>
<option value='11'>11</option>
<option value='12'>12</option>
<option value='13'>13</option>
<option value='14'>14</option>
<option value='15'>15</option>
<option value='16'>16</option>
<option value='17'>17</option>
<option value='18'>18</option>
<option value='19'>19</option>
<option value='20'>20</option>
<option value='21'>21</option>
<option value='22'>22</option>
<option value='23'>23</option>
<option value='24'>24</option>
<option value='25'>25</option>
<option value='26'>26</option>
<option value='27'>27</option>
<option value='28'>28</option>
<option value='29'>29</option>
<option value='30'>30</option>
<option value='31'>31</option>
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
  <input type=hidden name=db value='$pre'>
  <input type=hidden name=count value=''>
</form></td></tr></table>	<p><P><P><P><P>
";

echo "<th>REPORT FOR "; echo $pre; echo"</th>";
echo "<br><br>";

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
if (is_null($amount))
	{echo "No Tests Found After ".$day."/".$month."/20".$year."";
goto end;}

if($subcount == $count){
	echo "Showing failures for all "; echo $count +1; echo" Tests since: ".$day."/".$month."/20".$year."";
}
else{
echo "Showing Failures For "; echo $subcount +1; echo"  out of  "; echo $count +1; echo" Tests since: ".$day."/".$month."/20".$year."";}

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
	echo "Showing failures for all "; echo $actamount +1; echo" Tests";
}
else{
echo "Showing Report For The last ".$count." out of "; echo $actamount +1; echo"  tests since: ".$day."/".$month."/20".$year."<br><br>";}
}

/*


echo "<br>";
echo "<br>";
echo "<br>";
echo "<a href=#shorts>Shorts</a>";
echo "<br>";
echo "<a href=#analog>Analog</a>";
echo "<br>";
echo "<a href=#pins>Pins</a>";
echo "<br>";
echo "<a href=#testjet>Testjet</a>";
echo "<br>";
echo "<a href=#digital>Digital</a>";
echo "<br>";
echo "<br>";
echo "<br>";

*/

$shortamount = $conn->query("SELECT shorts_test_id FROM shorts_test WHERE test_id > $amount ORDER BY shorts_test_id ASC");
$shortamount = $shortamount->fetchArray();

if ($shortamount > "") {
echo "<table><th><a name='shorts'>Shorts Fails</a></th>
<tr><td  style='width:150'>Test Name</td><td  style='width:75'>Fail count</td></tr>";


$shorts = $conn->query("SELECT source, destination, count(*) FROM open_nodes WHERE shorts_test_id >= $shortamount[shorts_test_id] Group By source, destination ORDER BY count(*) DESC");

while($shortscount = $shorts->fetchArray()){

echo "<tr><td  style='width:150'>" . $shortscount["source"] ." to " . $shortscount["destination"] ."</td><td  style='width:75'>" . $shortscount["count(*)"] ."</td></tr>";

}
echo "</table>";
}
else
{echo "na";}

//analog

echo "<table><th><a name='analog'>Analog Fails</a><th>
<tr><td style='width:150'>Test Name</td><td style='width:75'>Fail count</td></tr>";

$analog = $conn->query("SELECT test_name, count(test_name) FROM analog WHERE test_id > $amount Group By test_name ORDER BY count(test_name) DESC ");

while($analogcount = $analog->fetchArray()){

echo "<tr><td style='width:150'><a href='analogreport.php?id=" . $analogcount["test_name"] ."&pre=".$pre."'>
" . $analogcount["test_name"] ."
</a></td><td style='width:75'>" . $analogcount["count(test_name)"] ."</td><tr>";
}
echo "</table>";


//pins

$pins = $conn->query("SELECT test_name, count(test_name) FROM pins_test WHERE test_id >  $amount Group By test_name ORDER BY count(test_name) DESC");
while($pinscount = $pins->fetchArray()){
if (!is_null($pinscount["test_name"])){
echo "<table><tr><th><a name='pins'>Pins Fails<th><tr>
<tr><td style='width:150'>Test Name</td><td style='width:75'>Fail count</td></tr>";
echo "<tr><td style='width:150'><a href='pinsreport.php?id=" . $pinscount["test_name"] ."&pre=".$pre."'>" . $pinscount["test_name"] ."</a></td><td  style='width:75'>" . $pinscount["count(test_name)"] ."</td><tr>";
}echo "</table>";}

//testjet

$testjet = $conn->query("SELECT test_name, count(test_name) FROM testjet_test WHERE test_id >  $amount Group By test_name ORDER BY count(test_name) DESC");
while($testjetcount = $testjet->fetchArray()){
if (!is_null($testjetcount["test_name"])){
echo "<table><TR><th><a name='testjet'>TestJet Fails<th></TR>
<tr><td style='width:150'>Test Name</td><td style='width:75'>Fail count</td></tr>";
echo "<tr><td style='width:150'>" . $testjetcount["test_name"] ."</td><td  style='width:75'>" . $testjetcount["count(test_name)"] ."</td><tr>";
}
echo "</table>";}

//digital

$digital = $conn->query("SELECT test_name, count(test_name) FROM digital_test WHERE test_id >  $amount Group By test_name ORDER BY count(test_name) DESC");
while($digitalcount = $digital->fetchArray()){
if (!is_null($digitalcount["test_name"])){
echo "<table><th><a name='digital'>Digital Fails<th>
<tr><td style='width:150'>Test Name</td><td style='width:75'>Fail count</td></tr>";

echo "<tr><td style='width:150'>" . $digitalcount["test_name"] ."</td><td  style='width:75'>" . $digitalcount["count(test_name)"] ."</td><tr>";
}
echo "</table>";}
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
end:
?>