<?php

// =================
// FILTRO DE PILOTOS
// =================

$piloto = "";
$where_piloto = "";
if(isset($p['piloto']))
{
    $piloto = $p['piloto'];
    $where_piloto = " AND (ownername1 LIKE '%" . $piloto . "%' OR ownername2 LIKE '%" . $piloto . "%')";
}

// =================
// INTERVALO DE DATAS
// =================

$dateIni = "";
$where_dateIni = "";
$split_timeIni = "";
if(isset($p['dateIni']) && $p['dateIni']!="")
{
    $dateIni = $p['dateIni'];
    $arr_dateIni = explode(" ", $dateIni);
    
    $split_dateIni = $arr_dateIni[0];
    if(isset($arr_dateIni[1])) $split_timeIni = " " . $arr_dateIni[1];
    
    $arr_split_dateIni = explode("/",$split_dateIni);
    
    $where_dateIni = " AND date >= '" . $arr_split_dateIni[2] . "-" . $arr_split_dateIni[1] . "-" . $arr_split_dateIni[0] . $split_timeIni . "'";
}

$dateFim = "";
$where_dateFim = "";
$split_timeFim = "";
if(isset($p['dateFim']) && $p['dateFim']!="")
{
    $dateFim = $p['dateFim'];
    $arr_dateFim = explode(" ", $dateFim);
    
    $split_dateFim = $arr_dateFim[0];
    if(isset($arr_dateFim[1])) $split_timeFim = " " . $arr_dateFim[1];
    
    $arr_split_dateFim = explode("/",$split_dateFim);
    
    $where_dateFim = " AND date <= '" . $arr_split_dateFim[2] . "-" . $arr_split_dateFim[1] . "-" . $arr_split_dateFim[0] . $split_timeFim . "'";
}

// ==========================
// GERACAO DA TABELA OWNER2
// ==========================

$query  = "SELECT ownerName2, sum(amount) as amount";
$query .= " FROM walletjournal wj";
$query .= " WHERE refTypeID=85 AND accountKey = 1000";
$query .= $where_dateIni;
$query .= $where_dateFim;
$query .= $where_piloto;
$query .= " GROUP BY ownerName2";
$query .= " ORDER BY amount DESC";

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$res = mysql_query($query,$conn) or die(mysql_error());

$tr = "";

$a=0;
$total_owner2 = 0;

while($linha = mysql_fetch_assoc($res))
{
    $tr .= "<tr>\r\n";
    
    $tr .= "<td>" . $linha['ownerName2'] ."</td>\r\n";
    $tr .= "<td>" . number_format($linha['amount'],2,",.",",") . "</td>\r\n";
      
    $tr .= "</tr>\r\n";
    
    $total_owner2 += $linha['amount'];
    $a++;
}

$tbody_owner2 = "<tbody>\r\n" . $tr . "</tbody>\r\n";

$thead_owner2 = "<thead>\r\n";
$thead_owner2 .= "<tr>\r\n";

$thead_owner2 .= "<th>Name</th>\r\n";
$thead_owner2 .= "<th>Amount</th>\r\n";


$thead_owner2 .= "</tr>\r\n";
$thead_owner2 .= "</thead>\r\n";


?>

<table id="tableOwner2" class="tablesorter" style="width: 350px">
    <?= $thead_owner2?>    
    <?= $tbody_owner2?>
</table>