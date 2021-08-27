<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script type='text/javascript' src="jquery/jquery.min.js"></script>

<style>
submit {
  border: 0px solid black;
   width: 500;
   background-color:#f6f6f6; color:#132D60;
}
</style>
<?php
include('header.php');
//sqllite
$serial = $_GET["id"];
$pre = substr("$serial", 0,3); 

$db = "$dir\\$pre\\repair_db.sqlite3";


if (file_exists($db)){
$conn = new SQLite3($db) ;
$results = $conn->query("SELECT * FROM btest WHERE serial='$serial' ORDER BY `btest`.`date` DESC");
$boardcheck = $results->fetchArray();
{if ($boardcheck != '') {
//fetch data from btest
echo "<br><br>Board ID: $serial<br>";
while($daterow = $results->fetchArray()) {
	echo "<br><a href=#" . $daterow["date"]. ">"; 

$day = substr($daterow['date'], 0,2); 	
$month = substr($daterow['date'], 2,2); 
$year = substr($daterow['date'], 4,2); 
$hour = substr($daterow['date'], 6,2); 
$minute = substr($daterow['date'], 8,2); 
$second = substr($daterow['date'], 10,2); 
echo "".$year."/".$month."/".$day."-".$hour.":".$minute.":".$second."</a> ";
};



if ($results->num_rows = 1) {

//output data of each row
while($row = $results->fetchArray()) {
echo "<submit><br><table>
<tr ><td>Board Position: " . $row["position"]. "</td>
<td >OPERATOR: " . $row["test_operator"]. "</td>
</tr>

<tr><td><a name=" . $row["date"]. "> TESTED: "; 
$day = substr($row['date'], 0,2); 	
$month = substr($row['date'], 2,2); 
$year = substr($row['date'], 4,2); 
$hour = substr($row['date'], 6,2); 
$minute = substr($row['date'], 8,2); 
$second = substr($row['date'], 10,2); 
echo $year;
echo "/";
echo $month; 
echo "/";
echo $day;
echo "-";
echo $hour;
echo ":";
echo $minute;
echo ":";
echo $second;
	echo "</td>
		<td> Tester ID: " . $row["tester_id"]. " </td></tr>" ;



//shorts


$btestid = $row["test_id"];
$sqlshortcheck = $conn->query("SELECT * FROM shorts_test WHERE test_id=$btestid ");

$shortcheck = $sqlshortcheck->fetchArray();
{if ($shortcheck != '') 
{echo "<tr>
		<td style=background-color:#FF6961>Shorts Test</td>";
	echo "<td style=background-color:#FF6961>FAIL</font>";
        echo "<br></tr>";
        $btestid = $row["test_id"];
$sqlshort = $conn->query("SELECT * FROM shorts_test WHERE test_id=$btestid ");
while($rowshort = $sqlshort->fetchArray()) {
$opensget =  $conn->query("SELECT * FROM open_nodes WHERE shorts_test_id='$rowshort[shorts_test_id]' ");
$opens = $opensget->fetchArray();	
echo "<tr><td>TESTNAME: <a href='shorts.php?id=" . $rowshort["shorts_test_id"]. "&pre="; echo $pre; echo"'>
" . $opens["source"]. " to " . $opens["destination"]. "</a></td>
</form></td>";
{if ($rowshort["comment"] > 0 ) {
echo "<td id='hidethis' style=background-color:#FFB347>REPAIR ATTEMPTED</td>" ;
}else
{if ($rowshort["view"] > 0 ) {
echo "	<td id='hidethis' style=background-color:#FFB347>VIEWED</td>" ;}}}
echo "</tr>";
}}
else
{echo "<tr><td style=background-color:#D6D6D6>Shorts Test</td>";
	echo "<td style=background-color:#77dd77>PASS</td>";		}};	
//{;		}};	
//pins	


$sqlpinscheck = $conn->query("SELECT * FROM pins_test WHERE test_id=$btestid ");
$pinscheck = $sqlpinscheck->fetchArray();	
{if ($pinscheck != '') 
	{echo "<tr align='left'>
		<td style=background-color:#FF6961>Pins Test</td>";
		echo "<td style=background-color:#FF6961>FAIL</font>";
        echo "<br></tr>";

$btestid = $row["test_id"];
$sqlpins = $conn->query("SELECT * FROM pins_test WHERE test_id=$btestid ");
while($rowpins = $sqlpins->fetchArray()) {
echo "<tr><td>TESTNAME: <a href='pins.php?id=" . $rowpins["pin_test_id"]. "&pre="; echo $pre; echo"'> " . $rowpins["test_name"]. "</a></td>
</form></td>";
{if ($rowpins["comment"] > 0 ) {
echo "<td id='hidethis' style=background-color:#FFB347>REPAIR ATTEMPTED</td>" ;
}else
{if ($rowpins["view"] > 0 ) {
echo "	<td id='hidethis' style=background-color:#FFB347>VIEWED</td>" ;}}}
echo "</tr>";
}}
else
 {echo "<tr align='left'>
<td style=background-color:#D6D6D6>Pins Test</td><td style=background-color:#77dd77>PASS</td>";}
//{;}

	};	

//analog
$sqlanalcheck = $conn->query("SELECT * FROM analog WHERE test_id=$btestid ");
$analcheck = $sqlanalcheck->fetchArray();	
{if ($analcheck != '') 
{echo "<tr>
		<td style=background-color:#FF6961>Analog Test</td>";
	echo "<td style=background-color:#FF6961>FAIL</font>";
//fetch test data
echo "<br></tr>";
$btestid = $row["test_id"];
$sqlanalog = $conn->query("SELECT * FROM analog WHERE test_id=$btestid ");
//		$resultanalog = $conn->query($sqlanalog);
while($rowanalog = $sqlanalog->fetchArray()) {
//click name to edit
echo "<tr><td>TESTNAME: <a href='analog.php?id=" . $rowanalog["analog_id"]. "&pre="; echo $pre; echo"'> " . $rowanalog["test_name"]. "</a></td>
</form></td>";
//checked?
{if ($rowanalog["comment"] > 0 ) {
echo "<td id='hidethis' style=background-color:#FFB347>REPAIR ATTEMPTED</td>" ;
}else
{if ($rowanalog["view"] > 0 ) {
echo "	<td id='hidethis' style=background-color:#FFB347>VIEWED</td>" ;}}}
echo "</tr>";
}}
else
{echo "<tr>
		<td style=background-color:#D6D6D6>Analog Test</td><td style=background-color:#77dd77>PASS</td>";}
//{;}
	};

//analog

//testjet
$sqltestjetcheck = $conn->query("SELECT * FROM testjet_test WHERE test_id=$btestid ");		
$testjetcheck = $sqltestjetcheck->fetchArray();	
{if ($testjetcheck != '') 
{echo "<tr>
		<td style=background-color:#FF6961>Testjet Tests</td><td style=background-color:#FF6961>FAIL</font>";

//fetch test data
echo "<br></tr>";
$btestid = $row["test_id"];
$sqltestjet = $conn->query("SELECT * FROM testjet_test WHERE test_id=$btestid ");
while($rowtestjet = $sqltestjet->fetchArray()) {
//click name to edit
echo "<tr><td>TESTNAME: <a href='testjet.php?id=" . $rowtestjet["testjet_id"]. "&pre="; echo $pre; echo"'> " . $rowtestjet["test_name"]. "</a></td>
</form></td>";
//checked?
{if ($rowtestjet["comment"] > 0 ) {
echo "<td id='hidethis' style=background-color:#FFB347>REPAIR ATTEMPTED</td>" ;
}else
{if ($rowtestjet["view"] > 0 ) {
echo "	<td id='hidethis' style=background-color:#FFB347>VIEWED</td>" ;}}}
echo "</tr>";
}}else
{echo "<tr><td style=background-color:#D6D6D6>Testjet Tests</td><td style=background-color:#77dd77>PASS</td>";}
//{;}
		;}

//testjet


//digital
$sqldigitalcheck = $conn->query("SELECT * FROM digital_test WHERE test_id=$btestid ");
$digitalcheck = $sqldigitalcheck->fetchArray();	
{if ($digitalcheck != '') 
{echo "<tr>
		<td style=background-color:#FF6961>Digital Test</td><td style=background-color:#FF6961>FAIL</font>";
//fetch test data
echo "<br></tr>";
$btestid = $row["test_id"];
$sqldigital = $conn->query("SELECT * FROM digital_test WHERE test_id=$btestid ");
while($rowdigital = $sqldigital->fetchArray()) {
//click name to edit
echo "<tr><td>TESTNAME: <a href='digital.php?id=" . $rowdigital["digital_id"]. "&pre="; echo $pre; echo"'> " . $rowdigital["test_name"]. "</a></td>
</form></td>";
//checked?
{if ($rowdigital["comment"] > 0 ) {
echo "<td id='hidethis' style=background-color:#FFB347>REPAIR ATTEMPTED</td>" ;
}else
{if ($rowdigital["view"] > 0 ) {
echo "	<td id='hidethis' style=background-color:#FFB347>VIEWED</td>" ;}}}
echo "</tr>";
}}else
{echo "<tr><td style=background-color:#D6D6D6>Digital Test</td><td style=background-color:#77dd77>PASS</td>";}
//{;}
		;}



//digital
echo	"</td></tr></br>";
echo	"</table></submit><a href='#top'>Go to top of page</a>";}}
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
}else	echo "BOARD ID '$serial' CANNOT BE FOUND<br>PLEASE CHECK THAT IT HAS BEEN ENTERED CORRECTLY (uppercase) AND THAT IT HAS BEEN TESTED";
}

$conn->connection = null;
}else {   echo "DATABASE FOR PCB '$pre' CANNOT BE FOUND<BR>
PLEASE CHECK THAT THE BARCODE HAS BEEN ENTERED CORRECTLY AND THAT THE BOARD HAS PREVIOUSLY BEEN TESTED";
}


?> 