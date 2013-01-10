<?php

header('Content-Type: text/html; charset=utf-8');

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

// encurtando a $_POST por motivos de preguica
$p = $_POST;

isset($p['preset']) ? $preset = $p['preset'] : $preset="";

// Trabalhar com Master Wallet por padrao
$accountKey = 1000;
if($preset!="ratter" && isset($p['accountKey']) && $p['accountKey']>=1000 && $p['accountKey'] <=1006)
    $accountKey = $p['accountKey'];

// Intervalo de datas

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
// PRESETS
// =================
$where_refTypeID = "";
if($preset=="ratter")
{
    // refTypeID deve ser 85 - somente bounties
    $where_refTypeID = " AND wj.refTypeID=85";
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
print_r($p);
$refType="";
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
    // $tr .= "<td>" . $linha['refID'] ."</td>\r\n";
    $tr .= "<td>" . $linha['refTypeName'] ."</td>\r\n";
    $tr .= "<td>" . $linha['ownerName1'] ."</td>\r\n";
    $tr .= "<td>" . $linha['ownerName2'] ."</td>\r\n";
    //$tr .= "<td>" . $linha['argName1'] ."</td>\r\n";
    //$tr .= "<td>" . $linha['argID1'] ."</td>\r\n";
    //$tr .= "<td>" . number_format($linha['amount'], 2, ",", ".")  ."</td>\r\n";
    
    $tr .= "<td>" . number_format($linha['amount'],2,".",",")  ."</td>\r\n";
    //$tr .= "<td>" . $linha['amount']  ."</td>\r\n";
    
//$tr .= "<td>" . number_format($linha['balance'], 2, ",", ".") ."</td>\r\n";
    $tr .= "<td>" . number_format($linha['balance'],2,".",",") ."</td>\r\n";
    //$tr .= "<td>" . $linha['reason'] . "</td>\r\n";
       
    $tr .= "</tr>\r\n";
    
    $total_all += $linha['amount'];
    $a++;
}

$tbody_all = "<tbody>\r\n" . $tr . "</tbody>\r\n";

$thead_all = "<thead>\r\n";
$thead_all .= "<tr>\r\n";

$thead_all .= "<th>Date</th>\r\n";
//$thead_all .= "<th>refID</th>\r\n";
$thead_all .= "<th>Tipo</th>\r\n";
$thead_all .= "<th>Origem</th>\r\n";
$thead_all .= "<th>Destino</th>\r\n";
//$thead_all .= "<th>argName1</th>\r\n";
//$thead_all .= "<th>argID1</th>\r\n";
$thead_all .= "<th>Amount</th>\r\n";
$thead_all .= "<th>Balance</th>\r\n";


$thead_all .= "</tr>\r\n";
$thead_all .= "</thead>\r\n";


// ==========================
// GERACAO DA TABELA OWNER1
// ==========================

$query  = "SELECT ownerName1, sum(amount) as amount";
$query .= " FROM walletjournal wj";
$query .= " WHERE accountKey = $accountKey";
$query .= $where_dateIni;
$query .= $where_dateFim;
$query .= $where_piloto;
$query .= " GROUP BY ownerName1";


$res = mysql_query($query,$conn) or die(mysql_error());

$tr = "";

$a=0;
$total_owner1 = 0;

while($linha = mysql_fetch_assoc($res))
{
    $tr .= "<tr>\r\n";
    
    $tr .= "<td>" . $linha['ownerName1'] ."</td>\r\n";
    $tr .= "<td>" . number_format($linha['amount'],2,".",",") . "</td>\r\n";
      
    $tr .= "</tr>\r\n";
    
    $total_owner1 += $linha['amount'];
    $a++;
}

$tbody_owner1 = "<tbody>\r\n" . $tr . "</tbody>\r\n";

$thead_owner1 = "<thead>\r\n";
$thead_owner1 .= "<tr>\r\n";

$thead_owner1 .= "<th>Name</th>\r\n";
$thead_owner1 .= "<th>Amount</th>\r\n";


$thead_owner1 .= "</tr>\r\n";
$thead_owner1 .= "</thead>\r\n";



// ==========================
// GERACAO DA TABELA OWNER2
// ==========================

$query  = "SELECT ownerName2, sum(amount) as amount";
$query .= " FROM walletjournal wj";
$query .= " WHERE accountKey = $accountKey";
$query .= $where_refTypeID;
$query .= $where_dateIni;
$query .= $where_dateFim;
$query .= $where_piloto;
$query .= " GROUP BY ownerName2";

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
        //source: ["","5EAN","Aierun Sun","ANK AC2","AyrFilipe","Bianca Souza","biracopus","Blade Wancer","Bob Heineken","BrnooX","Caldari Navy","CONCORD","Corporate Police Force","DarminghBR","DiWulfe","djomn mataloco","Dosh Moni","Earl van Gank","Ebura Hyotani","Einuard Erkeber","En Garde Management","evesnight","extraminador","Falanjer","Feeerz Olacar","felipelopes","Galahad Sagramor","GauchoDoSul","Grovelion","Guaianazes","GuilhermeMelo","Henrik Vanger","Ihala Ozunailen","Inogainen Shinatsu","Jammed Undies","Jonatas Alexandre","Kimuruola Sitsudan","Lanthes","Lee Xung","Lo bianco","Luke Skymining","MagnusIIIBR","MagnusIIITR","MineradorDoSul","Morgana Tsukiyo","Munashe","Mythus Supremus","Nehrnah Gorouyar","Noreena Somtaaw","Penadinho II","Pensador","Ponto Final","Qwed Gouda","Rage and Terror","rodrivaz","Ryan Steel","Sarum Family","Secure Commerce Commission","SHAAKR","SITHDAR","teresinense","Tick85","VisionCloud","XxNightCellearxX","Yuna Storm","Yuri Tsukimoto","Zambers","Zylla Maria"],
        minLength: 2
        //select: function( event, ui ) {
        //        log( ui.item ?
        //                "Selected: " + ui.item.value + " aka " + ui.item.id :
        //                "Nothing selected, input was " + this.value );
        //}
    });

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


</style>
 
<form action="<?= $_SERVER['SCRIPT_NAME']; ?>" method="post" name="filter">

<label for="preset">Presets</label>
<select name="preset">
    <option value=""></option>
    <option value="ratter">Ratter</option>
</select>

<label for="accountKey">Wallet</label>
<select name="accountKey"><option value=""></option><?= $optWalletDivisions?></select>

<br>

<label for="refType">Tipo</label>
<select name="refType"><option value=""></option><?= $optRefTypes?></select>

<br>

<label for="dateIni">Data Início</label>
<input type="text" id="dateIni" name="dateIni" value="<?= $dateIni?>">

<br>

<label for="dateIni">Data Fim</label>
<input type="text" id="dateFim" name="dateFim" value="<?= $dateFim?>">

<br>

<label for="piloto">Piloto</label>
<input type="text" name="piloto" id="piloto" value="<?= $piloto?>">


<input type="submit" value="Vai!">
</form>

    <table id="tableAll" class="tablesorter">
        <?= $thead_all?>    
        <?= $tbody_all?>
    </table>



<table id="tableOwner1" class="tablesorter" style="width: 350px">
    <?= $thead_owner1?>    
    <?= $tbody_owner1?>
</table>

<table id="tableOwner2" class="tablesorter" style="width: 350px">
    <?= $thead_owner2?>    
    <?= $tbody_owner2?>
</table>