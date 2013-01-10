<?php

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());


$query = "SELECT * FROM walletjournal";
$query = "SELECT * FROM walletjournal ORDER BY date DESC";

$res = mysql_query($query,$conn) or die(mysql_error());

$tr = "";

$a=0;

while($linha = mysql_fetch_assoc($res))
{
    $tr .= "<tr>\r\n";
    
    $tr .= "<td>" . $linha['date'] ."</td>\r\n";
    $tr .= "<td>" . $linha['refID'] ."</td>\r\n";
    $tr .= "<td>" . $linha['refTypeID'] ."</td>\r\n";
    $tr .= "<td>" . $linha['ownerName1'] ."</td>\r\n";
    $tr .= "<td>" . $linha['ownerName2'] ."</td>\r\n";
    $tr .= "<td>" . $linha['argName1'] ."</td>\r\n";
    $tr .= "<td>" . $linha['argID1'] ."</td>\r\n";
    $tr .= "<td>" . number_format($linha['amount'], 2, ",", ".")  ."</td>\r\n";
    $tr .= "<td>" . number_format($linha['balance'], 2, ",", ".") ."</td>\r\n";
       
    $tr .= "</tr>\r\n";
    
    $a++;
}

$tbody = "<tbody>" . $tr . "</tbody>";

$thead = "<thead>\r\n";
$thead .= "<tr>\r\n";

$thead .= "<th>date</th>\r\n";
$thead .= "<th>refID</th>\r\n";
$thead .= "<th>refTypeID</th>\r\n";
$thead .= "<th>ownerName1</th>\r\n";
$thead .= "<th>ownerName2</th>\r\n";
$thead .= "<th>argName1</th>\r\n";
$thead .= "<th>argID1</th>\r\n";
$thead .= "<th>amount</th>\r\n";
$thead .= "<th>balance</th>\r\n";


$thead .= "</tr>\r\n";
$thead .= "</thead>\r\n";


?>

<table border="1">
    <?=$thead?>    
    <?=$tbody?>
</table>

<?=$a?>