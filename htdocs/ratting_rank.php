<?php

// encurtando a $_POST por motivos de preguica
$p = $_POST;

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
$query .= " GROUP BY ownerName2";
$query .= " ORDER BY amount DESC";

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

echo $query;
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

<link rel="stylesheet" type="text/css" href="jquery/tablesorter/themes/blue/style.css" />

<script src="jquery/jquery.js"></script>
<script src="jquery/tablesorter/jquery.tablesorter.min.js"></script>

<script type="text/javascript" src="jquery/ui/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="jquery/ui/css/redmond/jquery-ui-1.8.23.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="jquery/ui/js/jquery-ui-timepicker-addon.js"></script>

<script>
jQuery.tablesorter.addParser({
  id: "commaDigit",
  is: function(s, table) {
    var c = table.config;
    return jQuery.tablesorter.isDigit(s.replace(/,/g, ""), c);
  },
  format: function(s) {
    return jQuery.tablesorter.formatFloat(s.replace(/,/g, ""));
  },
  type: "numeric"
});

$(document).ready(function(){
   
   $("#tableOwner2").tablesorter(
   {
       widgets: ['zebra'],
       sortList: [[1,1]],
       headers:{
           1: {sorter:'commaDigit'}
       }
   });
   
   $( "#dateIni" ).datetimepicker({
        
        hourGrid: 4,
        minuteGrid: 15,
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
        dayNamesMin: ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
        dayNames: ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"]
        
   });
   
   $( "#dateFim" ).datetimepicker({
        hourGrid: 4,
        minuteGrid: 15,
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
        dayNamesMin: ["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
        dayNames: ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sábado"]
   });
   
   
   
    $('#dateIni').wrap('<span class="deleteicon" />').after($('<span/>').click(function() {
            $(this).prev('input').val('').focus();
    }));
    
    $('#dateFim').wrap('<span class="deleteicon" />').after($('<span/>').click(function() {
            $(this).prev('input').val('').focus();
    }));
    
});
 </script>

<form action="<?= $_SERVER['SCRIPT_NAME']; ?>" method="post" name="filter">

<div class="divField">
<label for="dateIni">Data Inicio</label>
<input type="text" id="dateIni" name="dateIni" value="<?= $dateIni?>">
</div>

<div class="divField">
<label for="dateIni">Data Fim</label>
<input type="text" id="dateFim" name="dateFim" value="<?= $dateFim?>">
</div>

<input type="submit" value="Vai!">

</form>

<table id="tableOwner2" class="tablesorter" style="width: 350px">
    <?= $thead_owner2?>    
    <?= $tbody_owner2?>
</table>
 
 <b>Total:</b> <?= $total_owner2?>