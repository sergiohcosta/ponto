<?php

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$term = isset($_GET['term']) ? $_GET['term'] : "";

$sql = "SELECT * FROM (SELECT ownerName1 FROM walletjournal UNION SELECT ownerName2 FROM walletjournal ORDER BY ownerName1) AS tb WHERE tb.ownerName1 LIKE '%$term%'";

$res = mysql_query($sql,$conn);

$arr = array();
while($linha =  mysql_fetch_array($res))
{
    $arr[] = $linha[0];
}

echo json_encode($arr);