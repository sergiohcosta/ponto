<?php

header('Content-Type: text/html; charset=utf-8');

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

// encurtando a $_POST por motivos de preguica
$p = $_POST;

isset($p['preset']) ? $preset = $p['preset'] : $preset="";

// =================
// FILTRO DE WALLET
// =================
// Trabalhar com Master Wallet por padrao

$accountKey = 1000;
if($preset!="ratter" && isset($p['accountKey']) && $p['accountKey']>=1000 && $p['accountKey'] <=1006)
    $accountKey = $p['accountKey'];

// =================
// FILTRO DE DATAS
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
else { // caso a a data inicial nao seja fornecida, usar intervalo de uma semana (P7D) da data atual do servidor
    $dateIni = new DateTime();
    $dateIni->setTimezone(new DateTimeZone('Europe/London'));
    $dateIni->sub(new DateInterval('P7D'));
    $where_dateIni = " AND date >= '" . $dateIni->format('Y-m-d H:i:s') . "'";
    $dateIni = $dateIni->format('d/m/Y H:i:s');
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
else { // caso a a data final nao seja fornecida, usar data atual do servidor
    $dateFim = new DateTime();
    $dateFim->setTimezone(new DateTimeZone('Europe/London'));
    $where_dateFim = " AND date <= '" . $dateFim->format('Y-m-d H:i:s') . "'";
    $dateFim = $dateFim->format('d/m/Y H:i:s');
}

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
// walletdivisions: ARRAY E OPTIONS
// =================
$arrWalletDivisions = array();
$query = "SELECT * FROM walletdivisions";
$res = mysql_query($query,$conn) or die(mysql_error());

$optWalletDivisions = "";
while($linha = mysql_fetch_assoc($res))
{
    $arrWalletDivisions[$linha['accountKey']] = "'" . $linha['description'] . "'";
    $optWalletDivisions .= "<option";
    if($accountKey==$linha['accountKey']) $optWalletDivisions .= " selected";
    $optWalletDivisions .= " value='";
    $optWalletDivisions .= $linha['accountKey'];
    $optWalletDivisions .= "'>";
    $optWalletDivisions .= "(" . $linha['accountKey'] . ") " . $linha['description'];
    $optWalletDivisions .= "</option>";
}

// =================
// reftypes: SQL, ARRAY E OPTIONS
// =================

$refType="";
$where_refTypeID = "";
if(isset($p['refType']) && is_numeric($p['refType']))
{
    $refType=$p['refType'];
    $where_refTypeID = " AND wj.refTypeID=$refType";
    
}
 
$arrRefTypes = array();
$query = "SELECT * FROM reftypes";
$res = mysql_query($query,$conn) or die(mysql_error());

$optRefTypes = "";
while($linha = mysql_fetch_assoc($res))
{
    $arrRefTypes[$linha['refTypeID']] = "'" . $linha['refTypeName'] . "'";
    
    $optRefTypes .= "<option";
    if($refType==$linha['refTypeID']) { $optRefTypes .= " selected"; }
    $optRefTypes .= " value='";
    $optRefTypes .= $linha['refTypeID'];
    $optRefTypes .= "'>";
    $optRefTypes .= "(" . $linha['refTypeID'] . ") " . $linha['refTypeName'];
    $optRefTypes .= "</option>";
}

// =================
// REGISTROS DA WALLET
// =================

$query  = "SELECT * FROM walletjournal wj";
$query .= " LEFT JOIN reftypes rt ON wj.reftypeid=rt.reftypeid";
$query .= " WHERE accountKey = $accountKey";
$query .= $where_refTypeID;
$query .= $where_dateIni;
$query .= $where_dateFim;
$query .= $where_piloto;
$query .= " ORDER BY date DESC";

echo $query;

$res = mysql_query($query,$conn) or die(mysql_error());

$tr = "";

$a=0;
$total_all = 0;

while($linha = mysql_fetch_assoc($res))
{
    $tr .= "<tr>\r\n";
    
    $tr .= "<td>" . $linha['date'] ."</td>\r\n";
    $tr .= "<td>" . $linha['refTypeName'] ."</td>\r\n";
    $tr .= "<td>" . $linha['ownerName1'] ."</td>\r\n";
    $tr .= "<td>" . $linha['ownerName2'] ."</td>\r\n";
    
    $tr .= "<td>" . number_format($linha['amount'],2,".",",")  ."</td>\r\n";
    
    $tr .= "<td>" . number_format($linha['balance'],2,".",",") ."</td>\r\n";
    
    $tr .= "<td>" . $linha['argName1'] ."</td>\r\n";
    
    $reason = str_replace("DESC: ", "", $linha['reason']);
    if(strlen($linha['reason'])>20)
    {
         
        $reason = substr($reason, 0, 19) . " <a title='" . $reason . "'>(...)</a>";
    }
    $tr .= "<td>" . $reason ."</td>\r\n";
       
    $tr .= "</tr>\r\n";
    
    $total_all += $linha['amount'];
    $a++;
}

$tbody_all = "<tbody>\r\n" . $tr . "</tbody>\r\n";

$thead_all = "<thead>\r\n";
$thead_all .= "<tr>\r\n";

$thead_all .= "<th>Date</th>\r\n";
$thead_all .= "<th>Tipo</th>\r\n";
$thead_all .= "<th width='150'>Origem</th>\r\n";
$thead_all .= "<th width='150'>Destino</th>\r\n";
$thead_all .= "<th>Amount</th>\r\n";
$thead_all .= "<th>Balance</th>\r\n";
$thead_all .= "<th width='100'>O que/Onde</th>\r\n";
$thead_all .= "<th>Reason</th>\r\n";


$thead_all .= "</tr>\r\n";
$thead_all .= "</thead>\r\n";

?>

<link rel="stylesheet" type="text/css" href="jquery/tablesorter/themes/ponto/style.css" />

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
   
   $("#tableAll").tablesorter(
   {
       widgets: ['zebra'],
       sortList: [[0,1]],
       headers:{
           4: {sorter:'commaDigit'},
           5: {sorter:'commaDigit'}
       }
   });
   
   $("#tableOwner1").tablesorter({widgets: ['zebra']}); 
   
   $("#tableOwner2").tablesorter({
       widgets: ['zebra'],
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
    
    $('#piloto').wrap('<span class="deleteicon" />').after($('<span/>').click(function() {
            $(this).prev('input').val('').focus();
    }));


    $( "#piloto" ).autocomplete({
        source: "walletjournal_getpilots.php",
        minLength: 2
    });

    //$( document ).tooltip();
});
 </script>
 
<style>
    
div.ui-datepicker
{
    font-size:12px;
}

span.deleteicon {
    position: relative;
}
span.deleteicon span {
    position: absolute;
    display: block;
    top: 3px;
    right: 3px;
    width: 16px;
    height: 16px;
    background: url('img/sprites.png') 0 -690px;
    cursor: pointer;
}
span.deleteicon input {
    padding-right: 16px;
}

.divField {
    width: 500px;
    border: 1px solid black;
}

</style>
 
<form action="<?= $_SERVER['SCRIPT_NAME']; ?>" method="post" name="filter">

<div class="divField">
<label for="accountKey">Wallet</label>
<select name="accountKey"><option value=""></option><?= $optWalletDivisions?></select>
</div>

<div class="divField">
<label for="refType">Tipo</label>
<select name="refType"><option value=""></option><?= $optRefTypes?></select>
</div>

<div class="divField">
<label for="dateIni">Data Início</label>
<input type="text" id="dateIni" name="dateIni" value="<?= $dateIni?>">
</div>

<div class="divField">
<label for="dateIni">Data Fim</label>
<input type="text" id="dateFim" name="dateFim" value="<?= $dateFim?>">
</div>

<div class="divField">
<label for="piloto">Piloto</label>
<input type="text" name="piloto" id="piloto" value="<?= $piloto?>">
</div>

<input type="submit" value="Vai!">
</form>

    <table id="tableAll" class="tablesorter">
        <?= $thead_all?>    
        <?= $tbody_all?>
    </table>
